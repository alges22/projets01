<?php

namespace App\Http\Requests\Vehicle;

use App\Enums\VehicleCharacteristicCategoryType;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleCharacteristicCategoryRequest extends FormRequest
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
            'label' =>  ['required', 'string', 'max:255', Rule::unique('vehicle_characteristic_categories', 'label')->ignore($this->vehicle_characteristic_category?->id), new UniqueLabelSlugRule(VehicleCharacteristicCategory::class, $this->vehicle_characteristic_category?->id)],
            'type' => ['required', 'string', 'in:' . implode(',', VehicleCharacteristicCategoryType::toArray())],
        ];
    }
}
