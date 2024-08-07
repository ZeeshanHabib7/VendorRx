<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request) { 
        return [ 
            'id' => $this->id,
            'name'=> $this->name,
            'price'=> $this->price,
            'brand_id'=> $this->brand_id,
            'category_id' => $this->category_id,
            'cost' => $this->cost,
            'discount' => $this->discount,
            "stripe_product_id" => $this->stripe_product_id,
            "stripe_price_id" => $this->stripe_price_id,
        ];
    }
    
}