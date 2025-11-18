<?php

namespace App\Http\Requests;

use App\Enums\InstitutionTypesEnum;
use App\Enums\Status;
use App\Models\Institution\Institution;
use App\Models\Vehicle\GmaVehicle;
use App\Rules\Demands\ValidVINRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GmaVehicleRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $method = $this->input('_method');
        $this->merge(['author_id' => getOnlineProfile()->id]);

        $rules = [
            'vin' => [
                'required',
                new ValidVINRule,
                Rule::unique('gma_vehicles', 'vin')->ignore($this->gma_vehicle?->id),
                function ($attribute, $value, $fail) {
                    $gmaVehicle = GmaVehicle::find($this->gma_vehicle?->id);
                    if ($gmaVehicle?->status == Status::validated->name) {
                        $fail("Impossible de modifier un véhicule déjà validé.");
                    }
                },
            ],
            'author_id' => ['required', 'uuid', 'exists:profiles,id'],
            'customs_reference' => ['nullable', 'string',],
            'institution_id' => [
                'bail',
                'nullable',
                'exists:institutions,id',
                function ($attribute, $value, $fail) {
                    $institution = Institution::find($value);
                    if (
                        $institution &&
                        $institution?->type->name != InstitutionTypesEnum::io->name
                        &&
                        $institution?->type->name != InstitutionTypesEnum::ngo->name
                    ) {
                        $fail("Cette institution n'est pas affiliée aux affaires intérieures.");
                    }
                },
            ]
        ];

        if ($method && strtolower($method) == 'put') {
            $rules['declaration_file'] = ['nullable', 'file'];
        } else {
            $rules['declaration_file'] = ['required', 'file'];
        }

        return $rules;
    }
}
