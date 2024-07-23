<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest_SA;
use App\Http\Resources\Order_Resource_SA;
use App\Models\Order_SA;
use App\Models\OrderDetail_SA;
use App\Models\Products_SA;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\EncryptException;


class AddToCartController extends Controller
{
    public function store(AddToCartRequest_SA $request)
    {
        try {

            // Create a new order instance
            $order = new Order_SA;
            $order->reference_no = $this->generateReferenceNumber();
            $order->user_id = auth()->user()->id;
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

            //Initializing the fileds for our Order table
            $order->total_tax = $order->total_price * 0.17;
            $order->grand_total = $total + $order->total_tax;
            $order->paid_ammount = $order->grand_total;
            $order->payment_status = 'paid';
            $order->save();

            if ($request->header('isEncrypted') == true) {
                $data = $this->encryptData(Order_Resource_SA::make($order)->toJson());
            } else {
                $data = Order_Resource_SA::make($order);
            }


            return successResponse("Order added to cart successfully", $data);

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

    protected function encryptData($data)
    {
        return Crypt::encrypt($data);
    }


    // These two functions are for verifying the encrypted and decrypted data
    public function encrypt(Request $request)
    {
        try {

            $responseData = $request->getContent();
            $encryptedResponse = Crypt::encrypt($responseData);
            return successResponse("Encrypted Successfully", $encryptedResponse);
        } catch (EncryptException $e) {
            return errorResponse($e->getMessage());

        }

    }

    public function decrypt(Request $request)
    {
        try {
            $decryptedPayload = Crypt::decrypt($request->input('encryptedData'));
            $decodedPayload = json_decode($decryptedPayload, true);
            return successResponse("Decrypted Succesfully", $decodedPayload);

        } catch (DecryptException $e) {
            return errorResponse($e->getMessage());

        }

    }
}

