<?php

namespace App\Http\Requests\Vehicle;

use App\Models\PoliceOfficer\PoliceOfficer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class VehicleAlertRequest extends FormRequest
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
            'immatriculation_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (DB::table('immatriculations')->whereRaw('TRIM(number_label) = ?', [$value])->doesntExist())
                    {
                        $fail("Le numéro d'immatriculation est introuvable.");
                    }
                },
            ],
            'author_id' => ['required', 'uuid', 'exists:profiles,id'],
            'border_id' => ['required', 'uuid', 'exists:borders,id'],
            'driver_firstname' => ['required', 'string', 'max:255'],
            'driver_lastname' => ['required', 'string', 'max:255'],
            'alert_types' => ['required', 'array'],
            'alert_types.*' => ['required', 'uuid', 'exists:alert_types,id'],
            'comment' => ['nullable', 'string'],
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

        $this->merge(['author_id' => $onlineProfile->id]);
    }

    public function messages(): array
    {
        return [
            'border_id.required' => "Votre profile n'est pas lié à une frontière",
        ];
    }
}
