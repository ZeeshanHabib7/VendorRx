<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Return reponse if token exists 
        
        if(!empty($this->resource['token'])){
            return [
                'token' => $this->resource['token'],
                'user'=>[
                    'name' => $this->resource['user']['name'],
                    'email' =>$this->resource['user']['email']
                ]
            ];
        } 
        else {
            return parent::toArray($request);
        }
         
    }
}
