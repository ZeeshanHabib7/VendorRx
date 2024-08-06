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

    // relation with coupon codes
    public function couponCodes(){
        return $this->hasMany(CouponCode::class, 'coupon_id');
    }

    // customizing how the model behaves during various events in its lifecycle
    protected static function boot() {     
        parent::boot(); // calls the boot method of the parent class. It ensures that any boot logic defined in the parent class is also executed.
        
        static::deleting(function ($coupon) { // registers an event listener for the deleting event. The deleting event is fired when a model is about to be soft deleted.
            // Soft delete associated coupon codes
            $coupon->couponCodes()->each(function ($couponCode) {
                $couponCode->delete();
            });
        });
    }

    // Check if the coupon has been used
    public function isUsed()
    {
        return $this->couponUsages()->exists();
    }
}
