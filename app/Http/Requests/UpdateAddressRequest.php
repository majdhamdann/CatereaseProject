<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'city_id'    => 'sometimes|exists:cities,id',
            'district_id' => 'sometimes|exists:districts,id',
            'area_id'     => 'sometimes|exists:areas,id',
            'street'     => 'nullable|string',
            'building'   => 'nullable|string',
            'floor'      => 'nullable|string',
            'apartment'  => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

        ];
    }
}
