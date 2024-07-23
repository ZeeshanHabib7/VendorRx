<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order_SA extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $primaryKey = "order_id";

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail_SA::class, 'order_id', 'order_id');
    }
}
