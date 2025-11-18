<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ImmatriculationFormatRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $positions = collect($value)->pluck('position')->toArray();

        if (count(array_unique($positions)) != count($value))
        {
            $fail("Plusieurs composantes ont la même position");
        }
    }
}
