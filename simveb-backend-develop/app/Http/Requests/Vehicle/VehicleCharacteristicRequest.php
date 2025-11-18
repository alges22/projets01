<?php

namespace App\Http\Requests\Vehicle;

use App\Enums\VehicleCharacteristicCategoryType;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleCharacteristicRequest extends FormRequest
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
        $category = VehicleCharacteristicCategory::findOrFail($this->category_id);

        return [
            'category_id' => ['required', 'exists:vehicle_characteristic_categories,id'],
            "value" => ['nullable', 'string', Rule::requiredIf($this->checkCategoryType($category, VehicleCharacteristicCategoryType::string->name))],
            "min_value" => ['nullable', 'numeric', Rule::requiredIf($this->checkCategoryType($category, VehicleCharacteristicCategoryType::interval->name))],
            "max_value" => ['nullable', 'numeric', 'gte:min_value',  Rule::requiredIf($this->checkCategoryType($category, VehicleCharacteristicCategoryType::interval->name))],
            'cost' => ['nullable', 'numeric'],
        ];
    }

    /**
     * @param VehicleCharacteristicCategory|null $category
     * @param string $type
     * @return bool
     */
    public function checkCategoryType(VehicleCharacteristicCategory|null $category, string $type): bool
    {
        return $category && $category->type == $type;
    }
}
