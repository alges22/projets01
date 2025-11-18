<?php

namespace App\Rules\Demands;

use App\Repositories\Vehicle\VehicleRepository;
use App\Services\VehicleService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class VINIsUsedRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicle = (new VehicleRepository())->findWhere(['vin' => $value]);

        if ($vehicle){
            $fail("Le VIN que vous avez saisi est déjà utilisé");
        }
    }
}
