<?php

namespace App\Http\Requests\Vehicle;

use App\Enums\VehiclePassageType;
use App\Enums\VehicleTypeAtBorder;
use App\Models\PoliceOfficer\PoliceOfficer;
use App\Rules\Base64FileRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehiclePassageRequest extends FormRequest
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
            'officer_id' => ['required', 'uuid', 'exists:profiles,id'],
            'border_id' => ['required', 'uuid', 'exists:borders,id'],
            'vehicle_provenance' => [
                'required',
                'string',
                Rule::in(VehicleTypeAtBorder::toArray()),
            ],
            'total_passengers_on_board' => ['required', 'integer', 'gt:0'],
            'passage_type' => [
                'required',
                'string',
                Rule::in(VehiclePassageType::toArray()),
            ],
            'vehicle_owner_firstname' => ['required', 'string', 'max:255'],
            'vehicle_owner_lastname' => ['required', 'string', 'max:255'],
            'driver_firstname' => ['required', 'string', 'max:255'],
            'driver_lastname' => ['required', 'string', 'max:255'],
            'driver_telephone' => [
                'bail',
                Rule::excludeIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::local->name),
                Rule::requiredIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::external->name),
                'string',
            ],
            'vehicle_id' => [
                'bail',
                Rule::excludeIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::external->name),
                Rule::requiredIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::local->name),
                'string',
                'uuid',
                Rule::exists('vehicles', 'id'),
            ],
            'foreign_vehicle_immatriculation_number' => [
                'bail',
                Rule::excludeIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::local->name),
                Rule::requiredIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::external->name),
                'string',
            ],
            'immatriculation_country_id' => [
                'bail',
                Rule::excludeIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::local->name),
                Rule::requiredIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::external->name),
                'integer',
                'gt:0',
                Rule::exists('countries', 'id')
            ],
            'driving_license_number' => ['required', 'string'],
            'gray_card_number' => [
                Rule::requiredIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::external->name),
                Rule::excludeIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::local->name),
                'string'
            ],
            'driving_license_file' => ['required', new Base64FileRule],
            'gray_card_file' => [
                Rule::requiredIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::external->name),
                Rule::excludeIf(fn () => $this->vehicle_provenance === VehicleTypeAtBorder::local->name),
                new Base64FileRule,
            ],
        ];

    }

    protected function prepareForValidation(): void
    {
        $onlineProfile = getOnlineProfile();

        if ($onlineProfile->space?->border_id){
            $this->merge(['border_id' => $onlineProfile->space->border_id]);
        }else{
            $this->merge(['border_id' => $onlineProfile->institution?->border_id]);
        }

        $this->merge(['officer_id' => $onlineProfile->id]);
    }

    public function messages(): array
    {
        return [
            'officer_id.required' => 'Le profile du policiers est obligatoire',
            'officer_id.uuid' => 'Le profile du policiers est invalide',
            'officer_id.exists' => 'Le profile du policiers est invalide',
            'border_id.required' => "Votre profile n'est pas lié à une frontière",
        ];
    }

}
