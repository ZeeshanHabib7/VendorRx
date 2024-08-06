<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
    {   //Rule for Get/Fetch mrthod
        if ($this->isMethod('get')) {
            return [
                'name' => 'string',
                'usage_limit_min' => 'numeric|min:1',
                'usage_limit_min' => 'numeric',
                'product_id' => 'nullable',
                'status' => 'string|in:active,deactive',
                'stripe_price_id' => 'nullable|string',
                'expiry_before' => 'date',
                'expiry_after' => 'date',
                'usage_limit' => 'integer|min:1',
                'discount'=> 'integer',
                'discount_type' => 'string|in:percent,flat',
                'pageSize' => 'numeric|nullable|min:1',
                'pageNo' => 'numeric|nullable|min:1',
            ];
        }
        // Rule for create method
        else if ($this->isMethod('post')) {
            return [
                'name'=> 'required|string',
                'status' => 'string|in:active,deactive',
                'expiry' => 'required|date',
                'product_id' => 'nullable|exists:products,id',
                'code_count' => 'required|min:1',
                'usage_limit' => 'sometimes|integer|min:1',
                'usage_per_user' => 'sometimes|integer|min:1',
                'discount'=> 'required|integer',
                'discount_type' => 'string|in:percent,flat'
            ];
        }
        // Rule for update method
        elseif ($this->isMethod('patch') || $this->isMethod('put')) {
             $id = $this->route('id') ? $this->route('id') : null;
            return [
                'name' => 'required|string',
                'status' => 'string|in:active,deactive',
                'expiry' => 'required|date',
                'stripe_price_id' => 'nullable|string',
                'product_id' => 'nullable|exists:products,id',
                'discount'=> 'required|integer',
                'discount_type' => 'string|in:percent,flat',
                'coupon_codes' => 'nullable|array',
                'coupon_codes.*.id' => 'required|exists:coupon_codes,id',
                'coupon_codes.*.code' => [
                    'required',
                    'string',
                    Rule::unique('coupon_codes', 'code')->ignore($id)
                ],
                'coupon_codes.*.usage_limit' => 'sometimes|integer',
                'coupon_codes.*.usage_per_user' => 'sometimes|integer',

            ];
        }
    
        return []; 
       
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
