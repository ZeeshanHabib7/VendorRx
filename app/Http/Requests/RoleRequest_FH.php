<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleRequest_FH extends FormRequest
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
            return ['name' => 'required|unique:role,name,'];
        }
         // Rule for update method
        elseif ($this->isMethod('patch') || $this->isMethod('put')) {
            $id = $this->route('role') ? $this->route('role')->id : null;
            return [
                'name' => 'required|unique:roles,name,' . $id,
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
