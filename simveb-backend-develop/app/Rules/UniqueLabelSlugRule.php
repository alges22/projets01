<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class UniqueLabelSlugRule implements ValidationRule
{
    public function __construct(private readonly string $class, private readonly string|null $modelId)
    {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $name = Str::slug($value, '_');

        if ($this->class::where('name', $name)->exists() && $this->class::where('name', $name)->first()->id  != $this->modelId) {
            $fail("Veuillez changer la valeur du champ :attribute.");
        }
    }
}
