<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = "order_details";
    protected $primaryKey = "order_details_id";

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'net_unit_price',
        'total',
        'discount',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }


}
