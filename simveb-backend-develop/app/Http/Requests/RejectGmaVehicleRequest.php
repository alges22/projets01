<?php

namespace App\Http\Requests;

use App\Enums\Status;
use App\Models\Vehicle\GmaVehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RejectGmaVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return getOnlineProfile()->hasPermissionTo('reject-' . Str::kebab('GmaVehicle'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'gma_vehicles' => ['required','array'],
            'gma_vehicles.*.id' => [
                'exists:gma_vehicles,id',
                function ($attribute, $value, $fail) {
                    $gmaVehicle = GmaVehicle::find($value);
                    if ($gmaVehicle && $gmaVehicle->status == Status::validated->name) {
                        $fail("L'enregistrement de ce véhicule GMA est déjà validé.");
                    }
                },
            ],
            'gma_vehicles.*.reason' => ['required', 'string'],
        ];
    }
}
