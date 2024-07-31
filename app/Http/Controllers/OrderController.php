<?php

namespace App\Http\Controllers;


use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order_Resource;
use App\Models\Order;
use App\Models\User;
use Exception;


class OrderController extends Controller
{
    public function getOrderHistory(OrderRequest $request)
    {

        try {

            $userId = null;

            //If data is required by the 'User' role we will save his id
            if ($request->user()->hasRole('user'))
                $userId = getCurrentUserId();

            // If no query parameters    
            if (!$request->query()) {

                //Means we have to send all the data (If this is 'Admin' userId would be null and we will get all users data and if it is a 'User' than we will get only that user's data without any filters)
                $orders = Order::getOrders($userId)->get();

            } else {

                // If we have any query param, now we will get the filtered data acc to our query parameters
                $orders = Order::getFilterOrders($request, $userId);

                if (!$orders->exists()) {
                    // sending error response means we didn't find data for given filters 
                    return errorResponse("No orders fetched", 404);

                } else if ($request->paginate == true) {
                    // If we have data in our query and we also have pagination
                    $orders = $orders->paginate($request->pageSize);

                } else {
                    $orders = $orders->get();
                }



            }
            return successResponse("Orders fetched successfully", Order_Resource::collection($orders), $request->paginate, 200);



        } catch (Exception $e) {

            return errorResponse($e->getMessage(), 500);
        }

    }

    public function cancelOrder(OrderRequest $request)
    {

        try {
            $userId = getCurrentUserId();

            $order = Order::where('user_id', $userId)->find($request["orderId"]);

            $order->order_status = 'order_cancelled';

            $data = [
                "non_refundable_amount" => $order->grand_total,
                "msg" => "Cancelled order data",
                "order" => Order_Resource::make($order),
            ];

            $order->save();

            return successResponse("Your order has been successfully cancelled. Refund not available.", $data);     //code...

        } catch (Exception $e) {

            return errorResponse($e->getMessage(), 400);
        }


    }
}
