<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CouponRequest extends FormRequest
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
            'name'=> 'required|string',
            'status' => 'string|in:active,deactive',
            'expiry' => 'required|date',
            'product_id' => 'nullable|exists:products,id',
            'code_count' => 'required|min:1',
            'usage_limit' => 'sometimes|integer|min:1',
            'usage_per_user' => 'sometimes|integer|min:1',
            'discount'=> 'required|integer',
            'discount_type' => 'required|string|in:percent,flat'
        ];
    }

    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status_code' => 422,
            'message' => $validator->errors(),
            'data'    => []
        ], 422));
    }
}
