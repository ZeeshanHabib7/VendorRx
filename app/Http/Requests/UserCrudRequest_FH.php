<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCrudRequest_FH extends FormRequest
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
        // Rule for create method
        if ($this->isMethod('post')) {
           return [ 
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id' ];
        }
         // Rule for update method
        elseif ($this->isMethod('patch') || $this->isMethod('put')) {
            $id = $this->route('user') ? $this->route('user')->id : null;
            return [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8|confirmed',
                'role_ids' => 'sometimes|array',
                'role_ids.*' => 'exists:roles,id'
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
