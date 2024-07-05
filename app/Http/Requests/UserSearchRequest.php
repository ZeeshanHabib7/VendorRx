<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSearchRequest extends FormRequest
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
            'search_value' => 'nullable|string|max:255',
            'search_by' => 'nullable|string|in:name,email',
            'perPage' => 'sometimes|integer|min:1',
            'pagination' => 'sometimes|boolean',
            'page' => 'sometimes|integer|min:1',
            'type' => 'nullable|string|max:255',
        ];
    }
}
