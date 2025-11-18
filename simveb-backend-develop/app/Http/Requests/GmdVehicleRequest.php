<?php

namespace App\Http\Requests;

use App\Enums\InstitutionTypesEnum;
use App\Enums\Status;
use App\Models\Institution\Institution;
use App\Models\Vehicle\GmdVehicle;
use App\Rules\Demands\ValidVINRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GmdVehicleRequest extends FormRequest
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
        $method = $this->method();
        $this->merge(['author_id' => getOnlineProfile()->id]);

        $rules = [
            'vin' => ['required', new ValidVINRule, Rule::unique('gmd_vehicles', 'vin')->ignore($this->gmd_vehicle?->id),
                function ($attribute, $value, $fail) {
                    $gmdVehicle = GmdVehicle::find($this->gmd_vehicle?->id);
                    if ($gmdVehicle?->status == Status::validated->name) {
                        $fail("Impossible de modifier un véhicule déjà validé.");
                    }
                }
            ],
            'customs_reference' => ['nullable', 'string', ],
            'author_id' => ['required', 'uuid', 'exists:profiles,id'],
            'institution_id' => [
                'nullable',
                'uuid',
                Rule::exists('institutions', 'id'),
                function (string $attribute, mixed $value, Closure $fail) {
                    $institution = Institution::find($value);
                    if (
                        $institution &&
                        $institution?->type?->name !== InstitutionTypesEnum::embassie->name
                        &&
                        $institution?->type?->name !== InstitutionTypesEnum::consulate->name
                    ) {
                        $fail("Cette institution n'est pas affiliée à la diplomatie.");
                    }
                },
            ],
        ];

        if ($method && strtolower($method) == 'put') {
            $rules['declaration_file'] = ['nullable', 'required', 'file'];
        } else {
            $rules['declaration_file'] = ['required', 'file'];
        }

        return $rules;
    }
}
