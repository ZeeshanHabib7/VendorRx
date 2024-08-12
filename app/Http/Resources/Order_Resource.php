<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            "order_reference_no" => $this->reference_no,
            "order_id" => $this->order_id,
            "items" => $this->item,
            "order_date" => Carbon::parse($this->created_at)->format('j-F-Y, g:i a'),
            "order_status" => $this->order_status,
            "order_details" => Order_Details_Resource::collection($this->orderDetails),
            "address" => $this->address,
            "qty" => $this->total_quantity,
            "price" => round($this->total_price, 2),
            "order_discount_in_percent" => round($this->order_discount, 2),
            "total_discount_in_percent" => round($this->total_discount, 2),
            "tax" => round($this->total_tax, 2),
            "payment_status" => $this->payment_status,
            "grand_total" => round($this->grand_total, 2),

        ];
    }
}
