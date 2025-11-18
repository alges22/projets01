<?php

namespace App\Http\Requests;

use App\Enums\Status;
use App\Models\Vehicle\GmdVehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RejectGmdVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return getOnlineProfile()->hasPermissionTo('reject-' . Str::kebab('GmdVehicle'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'gmd_vehicles' => ['required','array'],
            'gmd_vehicles.*.id' => [
                'exists:gmd_vehicles,id',
                function ($attribute, $value, $fail) {
                    $gmaVehicle = GmdVehicle::find($value);
                    if ($gmaVehicle && $gmaVehicle->status == Status::validated->name) {
                        $fail("L'enregistrement de ce véhicule GMD est déjà validé.");
                    }
                },
            ],
            'gmd_vehicles.*.reason' => ['required', 'string'],
        ];
    }
}
