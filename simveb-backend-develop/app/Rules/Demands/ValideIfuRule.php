<?php

namespace App\Rules\Demands;

use App\Exceptions\UnexceptedErrorException;
use App\Services\IdentityService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class ValideIfuRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ctype_digit($value)) {
            $fail('Le champ :attribute doit contenir seulement des chiffres.');
        }

        if (strlen($value) != 13) {
            $fail(__('Le champ :attribute doit contenir 13 chiffres.'));
        }

        try {
            $checkIfuResponse = (new IdentityService())->getIdentityByIfu($value);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if (!isset($checkIfuResponse['email'])) {
            $fail(__('La valeur du champ :attribute est invalide.'));
        }
    }
}
