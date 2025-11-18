<?php

namespace App\Rules\Demands;

use App\Enums\Status;
use App\Models\Vehicle\Vehicle;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class VehicleIsImmatriculatedRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = Vehicle::query()->where('vin',$value)->first();

        if (!$vehicle?->immatriculation /*|| !in_array($vehicle->immatriculation->status, [Status::validated->name,Status::active->name,])*/){
            $fail("Oups! Impossible de faire cette demande. Votre véhicule ne possède pas une immatriculation valide");
        }
    }
}
