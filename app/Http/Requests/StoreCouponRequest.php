<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'branch_id' => 'required|exists:branches,id',
            'code' => 'required|string|unique:coupons,code|regex:/^[a-zA-Z0-9]{5,7}$/',
            'discount_amount' => 'required|numeric|min:0',
            'expiration_date' => 'required|date|after:today',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
