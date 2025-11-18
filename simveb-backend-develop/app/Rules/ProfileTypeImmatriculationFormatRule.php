<?php

namespace App\Rules;

use App\Repositories\Immatriculation\ImmatriculationFormatRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ProfileTypeImmatriculationFormatRule implements ValidationRule
{
    public function __construct(private  $skipId = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $format = (new ImmatriculationFormatRepository)->getFormatByProfileType($value);

        if ($format && $format->id != $this->skipId)
        {
            $fail("Un format d'immatriculation existe déjà pour ce type de profile");
        }
    }
}
