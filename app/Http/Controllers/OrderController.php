<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order_Resource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrderHistory(OrderRequest $request)
    {

        try {
            $userId = $request->user()->id;

            //If there are no query parameters we will return the whole data
            if (!$request->query()) {
                $orders = Order::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

            } else {

                //Now we will get the filtered data acc to our query parameters
                $query = Order::getFilterOrders($request, $userId);

                // If we have data in our query and we also have pagination
                if ($query->exists() && $request->paginate) {

                    $orders = $query->paginate($request->pageSize, ['*'], 'page', $request->pageNo);

                    // if only data is retrieved and no pagination
                } else if ($query->exists()) {

                    $orders = $query->get();

                } else {
                    // sending error response means we didn't find data for given filters 
                    return errorResponse("No orders fetched", 404);
                }
            }

            return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", Order_Resource::collection($orders), $request->paginate, $request->pageSize, $request->pageNo));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage(), 500);
        }

    }
}
