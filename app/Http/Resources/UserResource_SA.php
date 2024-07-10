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
    public function toArray(Request $request): array
    {
        // Return reponse if token exists 
        if (!empty($this->token)) {
            return [
                'token' => $this->token,
                'user' => [
                    'name' => $this->name,
                    'email' => $this->email
                ]
            ];
        } else {
            return parent::toArray($request);
        }

    }
}
