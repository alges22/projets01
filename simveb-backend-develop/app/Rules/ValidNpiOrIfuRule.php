<?php

namespace App\Rules;

use App\Exceptions\UnexceptedErrorException;
use App\Services\IdentityService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class ValidNpiOrIfuRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ctype_digit($value)) {
            $fail('Le champ :attribute doit contenir seulement des chiffres');
        }

        if (!in_array(strlen($value), [10, 13])) {
            $fail(__('Le champ :attribute doit contenir 10 ou 13 chiffres'));
        }

        try {
            $checkResponse = strlen($value) == 10
                ? (new IdentityService)->showByNpi($value)->response()->getData(true)['data']
                : (new IdentityService())->getIdentityByIfu($value);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if ((!isset($checkResponse['npi']) && !isset($checkResponse['ifu'])) || !isset($checkResponse['email'])) {
            $fail(__('La valeur du champ :attribute est invalide.'));
        }
    }
}
