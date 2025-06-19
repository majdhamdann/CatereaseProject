<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
           'Full_Name' => 'sometimes|string',
           'email' => 'sometimes|email|unique:users',
           'password' => 'sometimes|string|confirmed',
           'role_id' => 'sometimes|exists:roles,id',
           'phone' => 'sometimes|numeric',
           //'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
           'gender' => 'sometimes|in:f,m',
        ];
    }
}
