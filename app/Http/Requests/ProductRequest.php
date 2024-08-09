<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            'minPrice' => 'numeric|min:0',
            'maxPrice' => 'numeric',
            'brand' => 'nullable|string',
            'startDate' => 'date',
            'endDate' => 'date',
            'pageSize' => 'numeric|nullable|min:1',
            'pageNo' => 'numeric|nullable|min:1',
        ];

    }

    public function messages()
    {
        return [
            'name.string' => 'The name field must be a string',

            'brand.string' => 'The brand field must be a string',

            'price.integer' => 'The price field must be a integer',
            'price.min' => 'The price must be minimum of 10',
            'startDate.date' => 'The date field must be a valid date',
            'endDate.date' => 'The date field must be a valid date',
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
