<?php

namespace App\Http\Requests\packageManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageItemRequest extends FormRequest
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
           'package_id' => 'required|exists:packages,id',
           'items' => 'required|array',
           'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
           'items.*.is_optional' => 'nullable|boolean',
        ];
    }
}
