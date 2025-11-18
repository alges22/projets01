<?php

namespace App\Rules\Demands;

use App\Models\Vehicle\Vehicle;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class VehicleHasGrayCardRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = Vehicle::query()->where('vin', $value)->first();

        if (!$vehicle?->grayCard) {
            $fail('Impossible de faire cette demande. Votre véhicule ne possède pas de carte grise.');
        }
    }
}
