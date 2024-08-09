<?php

namespace App\Http\Controllers;

use App\Models\User;


use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\Order_Resource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Payment;
use App\Http\Interfaces\PaymentServiceInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth; // Add this for Auth
use Illuminate\Support\Collection;   // Add this for Collection
use App\Jobs\SendOrderConfirmationEmail; // Add this for SendOrderConfirmationEmail

class AddToCartController extends Controller
{

    protected $paymentService;

    // Injected Service 
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(AddToCartRequest $request)
    {
        DB::beginTransaction();
        try {

            // Get the authenticated user
            $user = getAuthenticatedUser();

            // Retrieve the card token from the request
            $cardToken = $request['card_token'];

            // Create stripe card payment method of user
            $card = $this->storePaymentMethod($cardToken, $user);

            // Save the card id in the user table
            $user->stripe_card_id = $card->id;
            $user->save();

            // Create a new order instance
            $order = new Order;
            $order->reference_no = $this->generateReferenceNumber();
            $order->user_id = $user->id;
            $order->address = $request['billing_address'];
            $order->order_discount = $request['order_discount'];
            $order->save();

            $products = $request->input("products");

            // Initialize total values
            $totalPrice = 0;
            $totalDiscount = 0;

            $orderDetailsCollection = new Collection(); // Initialize a collection for order details

            // Process each product
            foreach ($products as $product) {
                $order->total_quantity += $product['quantity'];
                //selecting only required data of the product
                $retrievedProduct = Product::select('id', 'name', 'price', 'discount', 'promotion')->find($product['product_id']);

                // If any product has a promotion, set order_discount to 0
                if ($retrievedProduct && $retrievedProduct->promotion) {
                    $order->order_discount = 0;
                }

                if ($retrievedProduct) {
                    //creating an Order_detail instance and initializing its fields
                    $orderDetails = OrderDetail::create([

                        "order_id" => $order->order_id,
                        "product_id" => $retrievedProduct->id,
                        "product_name" => $retrievedProduct->name,
                        "quantity" => $product['quantity'],
                        "net_unit_price" => $retrievedProduct->price,
                        "discount" => $retrievedProduct->discount,
                        "total" => $totalPrice,
                    ]);

                    $orderDetailsCollection->push($orderDetails); // Add order details to the collection

                    $order->total_price += $orderDetails->total;
                    $order->total_discount += $orderDetails->discount;
                    $order->item++;
                }
            }

            $total = $order->total_price - $order->total_price * ($order->order_discount / 100);


            //Initializing the fields for our Order table
            $order->total_tax = $order->total_price * 0.17;
            $order->grand_total = round($total + $order->total_tax,2);
            $order->paid_ammount = $order->grand_total;

            // Process Payment
            $payStatus = $this->processPayment($order->grand_total, $order->order_id, $user);

            // Check if payStatus is succeed
            if ($payStatus != "succeeded") {
                // Rollback transaction on error
                DB::rollBack();
                return errorResponse("Payment Failed! " . $payStatus, 402);
            }

            // save the payment status
            $order->payment_status = $payStatus;

            // save order in DB
            $order->save();

            if ($request->header('isEncrypted') == "true") {
                $data = $this->encryptData(Order_Resource::make($order)->toJson());
            } else {
                $data = Order_Resource::make($order);
            }


            // Send order confirmation email
            $user = Auth::user(); 
            if (!$user || !$user instanceof User) {
                throw new Exception("Authenticated user is not of type App\\Models\\User.");
            }
            dispatch(new SendOrderConfirmationEmail($orderDetailsCollection, $user));
            // Commit transaction
            DB::commit();

            return successResponse("Order added to cart successfully", $data);


        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    private function generateReferenceNumber()
    {
        $currentDate = Carbon::now()->format('Ymd');
        $currentTime = Carbon::now()->format('His');

        return $currentDate . 'DIP' . $currentTime;
    }

 // These two functions are for verifying the encrypted and decrypted




    public function storePaymentMethod($cardToken, $user)
    {
        try {
            // generate card payload
            $payload = $this->generateCardPayload($cardToken, $user);

            // Create a card for the customer using the token
            return $this->paymentService->createCard($payload);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function processPayment($amount, $orderId, $user)
    {
        try {
            // generate payment payload
            $payload = $this->generatePaymentPayload($amount, $user);

            // Create Stripe PaymentIntent
            $paymentIntent = $this->paymentService->createPaymentIntent($payload);


            // Check the status of the payment intent
            if ($paymentIntent->status == 'succeeded') {
                // Save payment details in DB
                $this->addPaymentDetails($amount, $orderId, $paymentIntent);
            }

            return $paymentIntent->status;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function generateCardPayload($cardToken, $user)
    {
        return [
            'customer_id' => $user->stripe_customer_id,
            'token' => $cardToken
        ];
    }

    public function generatePaymentPayload($amount, $user)
    {
        return [
            'amount' => $amount,
            'card_id' => $user->stripe_card_id,
            'customer_id' => $user->stripe_customer_id,
        ];
    }

    public function addPaymentDetails($amount, $orderId, $paymentIntent)
    {
        try {
            return Payment::create([
                'order_id' => $orderId,
                'payment_reference' => $paymentIntent->id,
                'amount' => $amount,
                'paying_method' => $paymentIntent['payment_method_types'][0],
                'payment_note' => $paymentIntent->status,
                'response' => json_encode($paymentIntent),
            ]);
        } catch (Exception $e) {
            return handleException($e);
        }
    }
}
