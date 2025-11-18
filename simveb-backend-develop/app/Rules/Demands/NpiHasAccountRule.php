<?php

namespace App\Rules\Demands;

use App\Models\Auth\Profile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ntech\UserPackage\Models\Identity;

class NpiHasAccountRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $identity = Identity::query()->where('npi', $value)->first();
        if ( $identity == null || Profile::query()->where('identity_id', $identity->id)->doesntExist()) {
            $fail(__("Ce NPI n'a pas encore de compte sur simveb."));
        }
    }
}
