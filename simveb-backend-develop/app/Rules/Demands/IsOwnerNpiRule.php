<?php

namespace App\Rules\Demands;

use App\Enums\ProfileTypesEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsOwnerNpiRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $profile = getOnlineProfile();
        if ($profile->type->code == ProfileTypesEnum::user->name && $profile->identity->npi != $value) {
            $fail(__('NPI inccorrect ou ne vous appatient pas'));
        }
    }
}
