<?php

namespace App\Rules\Demands;

use App\Models\Space\Space;
use App\Models\Auth\Profile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ntech\UserPackage\Models\Identity;

class IfuHasAccountRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $identity = Space::query()->where('ifu', $value)->first();
        if ( $identity == null || Profile::query()->where('identity_id', $identity->id)->doesntExist()) {
            $fail(__("Ce IFU n'a pas encore de compte sur simveb."));
        }
    }
}
