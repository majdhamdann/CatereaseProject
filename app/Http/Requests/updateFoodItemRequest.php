<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateFoodItemRequest extends FormRequest
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
             'category_id' => 'sometimes|required|integer',
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'discount_price' => 'nullable|numeric',
            'image_url' => 'nullable|string',
            'available' => 'boolean',
            'calories' => 'nullable|integer',
        ];
    }
}
