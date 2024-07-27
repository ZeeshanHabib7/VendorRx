<?php

namespace App\Models;

use App\Http\Requests\OrderRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $primaryKey = "order_id";

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function order()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected function getFilterOrders(OrderRequest $request, $userId = null)
    {
        try {

            if ($request->user()->hasRole('admin') && $request->has('userId')) {
                $userId = $request->userId;
            }

            $orders = $this->getOrders($userId);

            // If we have date in parameters then we will get data between that data or else we will get data between our default dates
            $orders = $orders->whereBetween('created_at', [$request->input('fromDate', '1990-01-01'), $request->input('toDate', date('Y-m-d H:i:s'))]);


            if ($request->has('orderById') && $request->orderById == true) {
                $orders = $orders->orderBy('order_id', 'asc');
            }



            // if ($request->has('fromDate')) {

            //     $fromDate = Carbon::parse($request->input('fromDate'))->startOfDay();
            //     $orders = $orders->where('created_at', '>=', $fromDate);
            // }

            // if ($request->has('toDate')) {
            //     $toDate = Carbon::parse($request->input('toDate'))->endOfDay();
            //     $orders = $orders->where('created_at', '<=', $toDate);
            // }

            if ($request->has('saleStatus')) {
                $orders = $orders->where('sale_status', $request->input('saleStatus'));
            }

            if ($request->has('paymentStatus')) {
                $orders = $orders->where('payment_status', $request->input('paymentStatus'));
            }

            return $orders;

        } catch (\Exception $e) {

            return errorResponse($e->getMessage(), 500);
        }
    }

    protected function getOrders($userId = null)
    {

        $orders = new Order;
        if (!is_null($userId)) {
            $orders = $orders->where('user_id', $userId);
        }

        $orders = $orders->orderBy('created_at', 'asc');

        return $orders;

    }
}
