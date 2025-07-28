<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryPersonRequest extends FormRequest
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
        $userId = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $userId,
            'gender' => 'sometimes|required|in:m,f',
            'photo' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
            'vehicle_type' => 'sometimes|required|string',
            'is_available' => 'sometimes|boolean',
        ];
    }
}
