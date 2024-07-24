<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest_SA;
use App\Http\Resources\Order_Resource_SA;
use App\Models\Order_SA;
use App\Models\OrderDetail_SA;
use App\Models\Products_SA;
use Carbon\Carbon;
use App\Models\Payment;
use App\Http\Interfaces\PaymentServiceInterface;
use App\Enums\PaymentStatus;

class AddToCartController extends Controller
{

    protected $paymentService;

    // Injected Service 
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(AddToCartRequest_SA $request)
    {
        try {
            
            // Get the authenticated user
            $user = getAuthenticatedUser();

            // Retrieve the card token from the request
            $cardToken = $request['card_token']; 

            // Create stripe card payment method of user
            $card = $this->storePaymentMethod($cardToken, $user);

           
            //check if card is created
            if(!isset($card->id)) {
                return errorResponse($card->getMessage(), $card->gethttpStatus());
            } 
                 
            // Save the card id in the user table
            $user->stripe_card_id = $card->id;
            $user->save();

            // Create a new order instance
            $order = new Order_SA;
            $order->reference_no = $this->generateReferenceNumber();
            $order->user_id = $user->id;
            $order->address = $request['billing_address'];
            $order->order_discount = $request['order_discount'];

            $order->save();

            $products = $request->input("products");


            //Now for each product in the request payload we will add that product in our order details table
            foreach ($products as $product) {
                $order->total_quantity += $product['quantity'];

                //selecting only required data of the product
                $retrievedProduct = Products_SA::select('id', 'name', 'price', 'discount', 'promotion')->find($product['product_id']);

                // if any product has a promotion than order_discount not applicable
                if ($retrievedProduct->promotion) {
                    $order->order_discount = 0;
                }

                if ($retrievedProduct) {

                    //creating an Order_detail instance and initializing its fields
                    $orderDetails = OrderDetail_SA::create([
                    "order_id" => $order->order_id,
                    "product_id" => $retrievedProduct->id,
                    "product_name" => $retrievedProduct->name,
                    "quantity" => $product['quantity'],
                    "net_unit_price" => $retrievedProduct->price,
                    "discount" => $retrievedProduct->discount,
                    "total" => $product['quantity'] * $retrievedProduct->price - $product['quantity'] * $retrievedProduct->price * ($retrievedProduct->discount / 100),
                    ]);

                    $order->total_price += $orderDetails->total;
                    $order->total_discount += $orderDetails->discount;
                    $order->item++;
                }
            }

            $total = $order->total_price - $order->total_price * ($order->order_discount / 100);

            // Initializing the fileds for our Order table
            $order->total_tax = $order->total_price * 0.17;
            $order->grand_total = $total + $order->total_tax;
            $order->paid_ammount = $order->grand_total;

            // Process Payment
            $payStatus = $this->processPayment($order->grand_total,$order->order_id, $user);

            // save the payment status
            $order->payment_status = $payStatus;

            // save order in DB
            $order->save();
            
            return successResponse("Order added to cart successfully", Order_Resource_SA::make($order));
            
        } catch (\Exception $e) {
            //if there is an error in processing the order than that order and order_deatils associated with it must be deleted
            $order->delete();
            return errorResponse($e->getMessage());
        }

    }

    private function generateReferenceNumber()
    {
        $currentDate = Carbon::now()->format('Ymd');
        $currentTime = Carbon::now()->format('His');

        return $currentDate . 'DIP' . $currentTime;
    }

    public function storePaymentMethod($cardToken, $user)
    {
        try {
            // Create a card for the customer using the token
            return $this->paymentService->createCard([
                'customer_id' => $user->stripe_customer_id,
                'token' => $cardToken
            ]);

        }
        catch (\Exception $e) {
            return handleException($e);
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
                return PaymentStatus::PAID;
            }
            else {
                // return PaymentStatus::UNPAID;
                return errorResponse($paymentIntent->status,401);
            }
  
        }
        catch (\Exception $e) {
            return handleException($e);
        }
    }

    public function generatePaymentPayload($amount, $user){
         return [
            'amount' => $amount,
            'card_id' => $user->stripe_card_id,
            'customer_id' => $user->stripe_customer_id,
         ];
    }

    public function addPaymentDetails($amount, $orderId, $paymentIntent){
        try{
            Payment::create([
                'order_id' => $orderId,
                'payment_reference' => $paymentIntent->id,
                'amount' => $amount,
                'paying_method' => $paymentIntent['payment_method_types'][0],
                'payment_note' => $paymentIntent->status,
            ]);
        }
        catch (\Exception $e) {
            return handleException($e);
        }
    }
}