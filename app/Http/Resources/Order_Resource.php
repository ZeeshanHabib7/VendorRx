<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order_Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "user_id" => $this->user_id,
            "order_no" => $this->reference_no,
            "items" => $this->item,
            "order_details" => Order_Details_Resource::collection($this->orderDetails),
            "address" => $this->address,
            "qty" => $this->total_quantity,
            "price" => round($this->total_price, 2),
            "order_discount_in_percent" => round($this->order_discount, 2),
            "total_discount_in_percent" => round($this->total_discount, 2),
            "tax" => round($this->total_tax, 2),
            "grand_total" => round($this->grand_total, 2),

        ];
    }
}
