<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderRequest extends FormRequest
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
            'orderById' => 'string|in:true,false',
            'fromDate' => 'date',
            'toDate' => 'date',
            'payment_status' => 'string|in:paid,unpaid',
            'sale_status' => 'string|in:order_placed,order_rejected,order_completed,order_dispatched,order_returned',
            'pageSize' => 'numeric|nullable|min:1',
            'pageNo' => 'numeric|nullable|min:1',
        ];
    }

    public function messages()
    {
        return [
            'orderById.in' => 'OrderById must be [ true or false ]',
            'payment_status.in' => 'Value must be [ paid or unpaid ]',
            'sale_status.in' => 'Value must be [ order_placed | order_rejected | order_completed | order_dispatched | order_returned ]',
            'toDate.date' => 'The date field must be a valid date',
            'fromDate.date' => 'The date field must be a valid date',
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
