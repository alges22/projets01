<?php

namespace App\Rules;

use App\Models\Auth\Invitation;
use App\Models\Auth\Profile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateGuestNpiRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $onlineProfile = auth()->user()->onlineProfile;
        $space = $onlineProfile->space;

        if (($space && $space->profiles()->whereRelation('user', 'username', '=', $value)->count() > 0)
            ||
            (!$space && Profile::where('type_id', $onlineProfile->type_id)->whereRelation('user', 'username', '=', $value)->count() > 0)
        ) {
            $fail(__("Cette personne appartient déjà à cet espace."));
        }

        $checkUnicityData = ['npi' => $value, 'profile_type_id' => $onlineProfile->type_id];

        if ($space) {
            $checkUnicityData += ['space_id' => $space->id];
        }

        if (Invitation::where($checkUnicityData)->count() > 0) {
            $fail(_('Cet utilisateur a déjà été invité dans cet espace.'));
        }
    }
}
