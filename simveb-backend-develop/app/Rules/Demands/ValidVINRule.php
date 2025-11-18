<?php

namespace App\Rules\Demands;

use App\Services\VehicleService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidVINRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = (new VehicleService)->checkVehicleExists(['vin' => $value]);

        if (!$vehicle){
            $fail("Le VIN que vous avez saisi est invalide");
        }
    }
}
