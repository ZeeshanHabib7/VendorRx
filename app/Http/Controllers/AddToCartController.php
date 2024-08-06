<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest_SA;
use App\Http\Resources\Order_Resource_SA;
use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Order_SA;
use App\Models\OrderDetail_SA;
use App\Models\Products_SA;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class AddToCartController extends Controller
{
    public function store(AddToCartRequest_SA $request)
    {
        try {
            // Create a new order instance
            $order = new Order_SA;
            $order->reference_no = $this->generateReferenceNumber();
            $order->user_id = Auth::id();
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

                // Retrieve product details
                $retrievedProduct = Products_SA::select('id', 'name', 'price', 'discount', 'promotion')->find($product['product_id']);

                // If any product has a promotion, set order_discount to 0
                if ($retrievedProduct && $retrievedProduct->promotion) {
                    $order->order_discount = 0;
                }

                if ($retrievedProduct) {
                    // Calculate total and discount
                    $totalPrice = $product['quantity'] * $retrievedProduct->price;
                    $discount = $product['quantity'] * $retrievedProduct->price * ($retrievedProduct->discount / 100);
                    
                    // Create OrderDetail_SA entry
                    $orderDetails = OrderDetail_SA::create([
                        "order_id" => $order->order_id,
                        "product_id" => $retrievedProduct->id,
                        "product_name" => $retrievedProduct->name,
                        "quantity" => $product['quantity'],
                        "net_unit_price" => $retrievedProduct->price,
                        "discount" => $retrievedProduct->discount,
                        "total" => $totalPrice - $discount,
                    ]);

                    $orderDetailsCollection->push($orderDetails); // Add order details to the collection

                    $order->total_price += $orderDetails->total;
                    $order->total_discount += $orderDetails->discount;
                    $order->item++;
                }
            }

            $total = $order->total_price - $order->total_price * ($order->order_discount / 100);

            // Initialize the fields for our Order table
            $order->total_price = $order->total_price;
            $order->total_discount = $order->total_discount;
            $order->total_tax = $order->total_price * 0.17;
            $order->grand_total = $total + $order->total_tax;
            $order->payment_status = 'paid';
            $order->save();

            // Send order confirmation email
            $user = Auth::user(); 
            dispatch(new SendOrderConfirmationEmail($orderDetailsCollection, $user));

            return response()->json(["message" => "Order added to cart successfully", "data" => Order_Resource_SA::make($order)]);

        } catch (\Exception $e) {
            // If there is an error in processing the order then that order and order_details associated with it must be deleted
            $order->delete();
            return response()->json(["error" => 'Failed to process the order.'], 500);
        }
    }

    private function generateReferenceNumber()
    {
        $currentDate = Carbon::now()->format('Ymd');
        $currentTime = Carbon::now()->format('His');

        return $currentDate . 'DIP' . $currentTime;
    }
}
