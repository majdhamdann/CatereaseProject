<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodItemRequest extends FormRequest
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
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
       'description' => 'nullable|string',
       'price' => 'required|numeric',
       'discount_price' => 'nullable|numeric',
       'photo' => 'nullable|string', // base64 image string
       'available' => 'required|boolean',
       'type' => 'nullable|in:veg,non_veg',
      ];

    }
}
