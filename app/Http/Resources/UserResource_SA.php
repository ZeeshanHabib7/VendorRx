<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource_SA extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Return reponse if token exists 
        $data = $this->resource;

        if ($data["token"]) {
            return [
                'token' => $data["token"],
                'user' => [
                    'name' => $data['user']->name,
                    'email' => $data['user']->email,
                ]
            ];
        } else {
            return [
                'user' => [
                    'name' => $data['user']->name,
                    'email' => $data['user']->email,
                ]
            ];
        }

    }
}
