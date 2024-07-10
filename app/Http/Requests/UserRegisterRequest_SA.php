<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|max:100|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Name field is required.',
            'name.string' => 'Enter a valid name in string.',
            'name.min' => 'Name must be greater than two characters.',
            'name.max' => 'The maximum length of name should be 255.',
            'email.required' => 'Email is required.',
            'email.string' => 'Enter a email in string.',
            'email.email' => 'Enter a valid email.',
            'email.unique' => 'This email already have an account. Please use another email',
            'password.required' => 'Password field is required for the registration.',
            'password.min' => 'Password must me minimum of 8 characters long.'
        ];
    }
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
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
