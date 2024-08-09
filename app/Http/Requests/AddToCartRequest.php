<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddToCartRequest extends FormRequest
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
            "products" => "required | array",
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            "billing_address" => "required|string",
            "card_token" => "required|string",
            "order_discount" => "integer",
        ];
    }

    public function messages()
    {
        return [
            "products.require" => "Products are required",
            "products.array" => "Products must be in array",
            "products.*.product_id.exists" => "Product does not exist in database",
            "products.*.quantity" => "Product quantity required"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status_code' => 422,
            'message' => $validator->errors(),
            'data' => []
        ], 422));
    }
}
