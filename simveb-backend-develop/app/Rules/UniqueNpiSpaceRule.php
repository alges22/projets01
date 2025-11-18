<?php

namespace App\Rules;

use App\Models\Auth\Invitation;
use App\Models\Auth\Profile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ntech\UserPackage\Models\Identity;
use Illuminate\Support\Facades\Log;

class UniqueNpiSpaceRule implements ValidationRule
{

    public function __construct(private $space_id)
    {
        $this->space_id = $space_id ?? getOnlineProfile()->space_id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $identity = Identity::where('npi', $value)->first();
        $profile = $identity ? Profile::where('identity_id', $identity?->id)->where('space_id', $this->space_id)->first() : null;
        $invitation = Invitation::where('npi', $value)->where('space_id', $this->space_id)->first();

        if (isset($profile) && isset($identity)) {
            $fail(__("Cette personne appartient déjà à cet espace."));
        }

        if (isset($invitation) && !$invitation->denied) {
            $fail(_('Cet utilisateur a déjà été invité dans cet espace.'));
        }
    }
}
