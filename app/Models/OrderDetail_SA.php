<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail_SA extends Model
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
        return $this->belongsTo(Order_SA::class, 'order_id', 'order_id');
    }


}
