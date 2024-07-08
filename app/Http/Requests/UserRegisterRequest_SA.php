<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest_SA extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $validate = [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|max:100|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ];

        dd($validate);

        return $validate;
    }
}
