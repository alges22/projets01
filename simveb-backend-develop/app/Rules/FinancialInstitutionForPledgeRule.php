<?php

namespace App\Rules;

use App\Consts\Roles;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FinancialInstitutionForPledgeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $profile = getOnlineProfile();

        if (!$profile->hasRole(Roles::DISTRIBUTOR) && !$profile->hasPermissionTo('store-pledge-by-distributor')) {
            $fail("Ce profil n'est pas autorisé à mettre un véhicule sous gage avec une institution financière associée");
        }
    }
}
