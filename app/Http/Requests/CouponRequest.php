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
                'usage_limit_max' => 'numeric', // corrected min to max
                'product_id' => 'nullable',
                'status' => 'string|in:active,deactive',
                'stripe_price_id' => 'nullable|string',
                'expiry_before' => 'date',
                'expiry_after' => 'date',
                'usage_limit' => 'integer|min:1',
                'discount'=> 'numeric',
                'discount_type' => 'string|in:percent,flat',
                'perPage' => 'sometimes|integer|min:1',
                'pagination' => 'sometimes|boolean',
                'page' => 'sometimes|integer|min:1',
            ];
        }
        // Rule for create method
        else if ($this->isMethod('post')) {
            return [
                'name'=> 'required|string',
                'status' => 'string|in:active,deactive',
                'expiry' => 'required|date',
                'product_id' => 'nullable|exists:products,id',
                'usage_limit' => 'sometimes|integer|min:1',
                'usage_per_user' => 'sometimes|integer|min:1',
                'discount'=> 'required|numeric',
                'discount_type' => 'string|in:percent,flat',
                'is_multi' => 'required|boolean',  
                'code_count' => 'required_if:is_multi,true|nullable|integer|min:2',
                'code' => 'required_if:is_multi,false|nullable|string'
            ];
        }
        // Rule for update method
        elseif ($this->isMethod('patch') || $this->isMethod('put')) {
            return [
                'name' => 'required|string',
                'status' => 'string|in:active,deactive',
                'expiry' => 'required|date',
                'product_id' => 'nullable|exists:products,id',
                'discount'=> 'required|numeric',
                'discount_type' => 'string|in:percent,flat',
                'coupon_codes.*.id' => 'required|exists:coupon_codes,id',
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
