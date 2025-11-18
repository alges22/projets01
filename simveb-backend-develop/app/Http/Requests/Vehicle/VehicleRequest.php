<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    private $portalCreation;
    private $vehicleId;

    public function __construct($portalCreation = false, $vehicleId = null)
    {
        $this->vehicleId = $vehicleId;
        $this->portalCreation = $portalCreation;
    }

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
        if ($this->portalCreation) {
            $id = $this->vehicleId;
        } else {
            $id = $this->filled('id') ? $this->id : $this->vehicle?->id;
        }

        return [
            'origin_country_id' => ['nullable','exists:countries,id'],
            'vin' => ['required',Rule::unique('vehicles','vin')->ignore($id)],
            'customs_reference' => ['nullable',Rule::unique('vehicles','customs_reference')->ignore($id)],
            'vehicle_brand_id' => ['nullable','exists:vehicule_brands,id'],
            'vehicle_model' => ['nullable','string'],
            'number_of_seats' => ['nullable','numeric','min:0'],
            'vehicle_type_id' => ['nullable','exists:vehicle_types,id'],
            'owner_type_id' => ['nullable','exists:owner_types,id'],
            'owner_id' => ['nullable','exists:vehicle_owners,id'],
            'vehicle_category_id' => ['nullable','exists:vehicle_categories,id'],
            'engin_number' => ['nullable'],
            'empty_weight' => ['nullable','numeric','min:0'],
            'charged_weight' => ['nullable','numeric','gt:empty_weight'],
            'first_circulation_year' => ['nullable','numeric'],
            'vehicle_characteristics' => ['nullable','array'],
            'vehicle_characteristics.*' => ['exists:vehicle_characteristics,id'],
            'park_name' => ['nullable', 'string'],
            'energy_type_id' => ['nullable', 'exists:vehicule_energy_sources,id'],
        ];

    }
}
