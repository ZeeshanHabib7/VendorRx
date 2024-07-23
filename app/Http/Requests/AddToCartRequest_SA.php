<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddToCartRequest_SA extends FormRequest
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
        // dd($this->request);
        return [
            "products" => "required | array",
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            "billing_address" => "required|string",
            "card_id" => "required|string",
            "order_discount" => "integer",
        ];
    }

    public function messages()
    {
        return [
            "products.require" => "Products are required",
            "products.array" => "Products must be in array",
            "products.*.product_id.exists" => "Product doesnot exist in database",
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

// {
//     "products": [
//         {
//             "product_id": 5,
//             "quantity": 1
//         },
//         {
//             "product_id": 7,
//             "quantity": 1
//         }
//     ],
//     "billing_address_id": 18215,
//     "use_billing_address_for_shipping": 1,
//     "user_id": 142,
//     "card_id": "card_1Oix9n2eZvKYlo2CAmnsF22d"
// }