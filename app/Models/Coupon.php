<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'expiry',
        'product_id',
        'stripe_price_id',
        'discount',
        'discount_type'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function couponCodes()
    {
        return $this->hasMany(CouponCode::class, 'coupon_id');
    }
}
