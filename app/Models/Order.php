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

    protected function getFilterOrders(OrderRequest $request, $userId)
    {
        try {

            $query = Order::query()->where('user_id', $userId);

            if ($request->has('orderById') && $request->input('orderById') == true) {
                $query->orderBy('order_id', 'desc');
            }

            if ($request->has('fromDate')) {
                $fromDate = Carbon::parse($request->input('fromDate'))->startOfDay();
                $query->where('created_at', '>=', $fromDate);
            }

            if ($request->has('toDate')) {
                $toDate = Carbon::parse($request->input('toDate'))->endOfDay();
                $query->where('created_at', '<=', $toDate);
            }

            if ($request->has('saleStatus')) {
                $query->where('sale_status', $request->input('saleStatus'));
            }

            if ($request->has('paymentStatus')) {
                $query->where('payment_status', $request->input('paymentStatus'));
            }

            return $query;

        } catch (\Exception $e) {

            return errorResponse($e->getMessage(), 500);
        }
    }
}
