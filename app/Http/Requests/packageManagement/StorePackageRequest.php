<?php

namespace App\Http\Requests\packageManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'branch_service_type_ids' => 'required|array',
            'branch_service_type_ids.*' => 'exists:branch_service_types,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'required|string',
            'base_price' => 'required|numeric',
            'serves_count' => 'required|integer',
            'max_extra_persons' => 'required|integer',
            'price_per_extra_person' => 'required|numeric',
            'cancellation_policy' => 'required|string',
            'prepayment_required' => 'boolean',
            'prepayment_amount' => 'required|numeric',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'occasion_type_ids' => 'required|array',
            'occasion_type_ids.*' => 'exists:occasion_types,id',
            'items' => 'array',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.is_optional' => 'boolean',
            'extras' => 'array',
            'extras.*.type' => 'required|in:food_item,service,simple',
            'extras.*.name' => 'required|string',
            'extras.*.price' => 'required|numeric',
            'extras.*.is_optional' => 'boolean',
            'extras.*.food_item_id' => 'required|exists:food_items,id',
            'extras.*.branch_service_type_id' => 'required|exists:branch_service_types,id',
    ];
    }
}
