<?php

namespace App\Http\Requests\packageManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackagemanageRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => 'sometimes|exists:branches,id',
            'branch_service_type_ids' => 'nullable|array',
            'branch_service_type_ids.*' => 'exists:branch_service_types,id',

            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',

            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',

            'occasion_type_ids' => 'nullable|array',
            'occasion_type_ids.*' => 'exists:occasion_types,id',

            'base_price' => 'nullable|numeric|min:0',
            'serves_count' => 'nullable|integer|min:0',
            'max_extra_persons' => 'nullable|integer|min:0',
            'price_per_extra_person' => 'nullable|numeric|min:0',

            'cancellation_policy' => 'nullable|string',
            'prepayment_required' => 'nullable|boolean',
            'prepayment_amount' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.exists' => 'الفرع غير موجود.',
            'branch_service_type_ids.*.exists' => 'نوع الخدمة غير صالح.',
            'category_ids.*.exists' => 'الفئة المحددة غير موجودة.',
            'occasion_type_ids.*.exists' => 'نوع المناسبة المحدد غير موجود.',
        ];
    }
}
