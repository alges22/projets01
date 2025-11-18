<?php

namespace App\Rules;

use App\Models\Config\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsInterpolServiceRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = Organization::find($value);

        if (!$service || !$service->is_interpol){
            $fail("Ce service ne semble pas être un service d'interpole");
        }
    }
}
