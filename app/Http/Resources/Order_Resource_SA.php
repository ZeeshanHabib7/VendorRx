<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order_Resource_SA extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "user_id" => auth()->user()->id,
            "order_no" => $this->reference_no,
            "items" => $this->item,
            "order_details" => Order_Details_Resource_SA::collection($this->orderDetails),
            "address" => $this->address,
            "qty" => $this->total_quantity,
            "price" => $this->total_price,
            "order_discount_in_percent" => $this->order_discount,
            "total_discount_in_percent" => $this->total_discount,
            "tax" => round($this->total_tax, 2),
            "grand_total" => $this->grand_total,

        ];
    }
}
