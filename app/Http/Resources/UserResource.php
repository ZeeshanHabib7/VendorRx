<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    // private $token;
    // private $user;

    // public function __construct($resource)
    // {
    //     $this->token = $resource['token'];
    //     $this->user = $resource['user'];
    // }

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
