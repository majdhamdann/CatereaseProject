<?php

namespace App\Http\Requests\packageManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageExtraRequest extends FormRequest
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
        'extras' => 'required|array',
        'extras.*.type' => 'nullable|string',
        'extras.*.food_item_id' => 'nullable|exists:food_items,id',
        'extras.*.branch_service_type_id' => 'nullable|exists:branch_service_types,id',
        'extras.*.name' => 'nullable|string',
        'extras.*.price' => 'nullable|numeric',
        'extras.*.is_optional' => 'nullable|boolean',
    ];
    }
}
