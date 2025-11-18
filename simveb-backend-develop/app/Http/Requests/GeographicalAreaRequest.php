<?php

namespace App\Http\Requests;

use App\Models\Config\GeographicalArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeographicalAreaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', Rule::unique('geographical_areas', 'name')->ignore($this->route()->parameter('geographical_area'))],
            'description' => ['string'],
            'type' => [Rule::in([GeographicalArea::CITY, GeographicalArea::REGION, GeographicalArea::COUNTRY])],
            'code' => ['string', Rule::unique('geographical_areas', 'code')->ignore($this->route()->parameter('geographical_area'))],
            'authorized_registration_format' => ['string'],
            'validation_criteria' => ['string'],
            'restrictions_or_special_requirements' => ['string'],
            'staff_ids' => ['array'],
            'staff_ids.*' => ['uuid', 'distinct']
        ];
    }
}
