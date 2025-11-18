<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleAdministrationRequest extends FormRequest
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
            'vin' => ['sometimes', 'required', 'prohibits:immatriculation', 'exists:vehicles,vin'],
            'immatriculation' => ['sometimes', 'required', 'prohibits:vin', function ($attribute, $value, $fail) {
                $vehicle = getVehicleByImmatriculation($value);

                if (!$vehicle) {
                    $fail('Immatriculation invalide.');
                }
            }],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "*.prohibits" => "Veuillez entrer soit un VIN ou soit un numero d'immatriculation",
        ];
    }
}
