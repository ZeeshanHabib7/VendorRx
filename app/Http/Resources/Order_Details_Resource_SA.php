<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order_Details_Resource_SA extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->order_details_id,
            "order_id" => $this->order_id,
            "product_id" => $this->product_id,
            "product_name" => $this->product_name,
            "quantity" => $this->quantity,
            "net_unit_price" => $this->net_unit_price,
            "discount_in_percent" => $this->discount,
            "product_total_ammount" => $this->total,


        ];
    }
}
// "order_details_id": 33,
//                 "order_id": 20,
//                 "product_id": 50,
//                 "product_name": "Baggy Shirt",
//                 "quantity": 2,
//                 "net_unit_price": 2000,
//                 "discount": 10,
//                 "total": 3998,
//                 "created_at": "2024-07-22T10:59:30.000000Z",
//                 "updated_at": "2024-07-22T10:59:30.000000Z"
