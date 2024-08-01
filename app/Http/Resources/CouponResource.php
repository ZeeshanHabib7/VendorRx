<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "status" => $this->status ?? 'deactive',
            "expiry" => $this->expiry,
            "product_id" => $this->product_id,
            "stripe_price_id" => $this->stripe_price_id,
            "discount" => $this->discount,
            "discount_type" => $this->discount_type,
            "coupon_codes"=> CouponCodeResource::collection($this->couponCodes),
        ];
    }
}
