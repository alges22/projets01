<?php

namespace App\Http\Requests\Vehicle;

use App\Models\Vehicle\VehicleType;
use App\Rules\UniqueLabelSlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleTypeRequest extends FormRequest
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
            'label' =>  ['required', 'string', 'max:255', Rule::unique('vehicle_types', 'label')->ignore($this->vehicle_type?->id), new UniqueLabelSlugRule(VehicleType::class, $this->vehicle_type?->id)],
        ];
    }
}
