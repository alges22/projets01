<?php

namespace App\Rules;

use App\Repositories\Immatriculation\ImmatriculationFormatRepository;
use App\Services\Immatriculation\ImmatriculationFormatService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VehicleCategoryImmatriculationFormatRule implements ValidationRule
{
    public function __construct(private ?string $profileTypeId, private  $skipId = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $format = (new ImmatriculationFormatRepository())->getFormatByVehicleCategoryAndProfile($value, $this->profileTypeId);

        if ($format && $format->id != $this->skipId)
        {
            $fail("Un format d'immatriculation existe déjà pour cette catégorie de véhicule");
        }
    }
}
