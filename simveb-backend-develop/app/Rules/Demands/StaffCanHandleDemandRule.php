<?php

namespace App\Rules\Demands;

use Closure;
use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Models\Auth\Role;
use App\Models\Auth\Profile;
use Illuminate\Contracts\Validation\ValidationRule;

class StaffCanHandleDemandRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $staff = Profile::query()
        ->whereRelation('type', 'code', ProfileTypesEnum::anatt->name)
        ->whereHas('identity', fn($query) => $query->where('npi', $value))
        ->first();

        if(!$staff || !$staff->hasRole(Roles::DEMAND_MANAGER)){
            $fail("Cet utilisateur n'a pas habileté pour traiter une demande");
        }
    }
}
