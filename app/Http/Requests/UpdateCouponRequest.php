<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('id');

        return [
            'code' => 'sometimes|required|string|unique:coupons,code,' . $couponId,
            'discount_amount' => 'sometimes|required|numeric|min:0',
            'expiration_date' => 'sometimes|required|date|after:today',
            'user_id' => 'nullable|exists:users,id',
            'package_ids' => 'required|array',
            'package_ids.*' => 'exists:packages,id',
        ];
    }
}