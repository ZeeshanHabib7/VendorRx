<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'coupon_id',
        'code',
        'usage_limit',
        'usage_per_user'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

     // relationship with the coupon model
     public function coupon()
     {
         return $this->belongsTo(Coupon::class);
     }

     // relationship with the CouponUsage model
     public function couponUsages()
     {
        return $this->hasMany(CouponUsage::class);
     }
     
}
