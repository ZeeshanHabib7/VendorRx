<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return [
            'name' => 'string',
            'price' => 'numeric|min:0|max:999999.99',
            'date' => 'date|nullable',
            'brand' => 'string|nullable',
        ];
    }

    public function messages(){
        return[
            'name.string' => 'The name field must be a string',

            'brand.string' => 'The brand field must be a string',

            'price.integer' => 'The price field must be a integer',
            'price.min' => 'The price must be minimum of 10',

            'date.date' => 'The date field must be a valid date',
        ];
    }
}
