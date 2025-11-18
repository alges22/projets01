<?php

namespace App\Rules;

use App\Imports\ValidationImport;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateExcelFileRule implements ValidationRule
{
    public function __construct(private readonly array $rules = [], private readonly array $customValidationMessages = [], private readonly int $headRow = 1) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $file, Closure $fail): void
    {
        if ($file) {
            $import = new ValidationImport($this->rules, $this->customValidationMessages, $this->headRow);
            $import->import($file);

            foreach ($import->failures() as $failure) {
                foreach ($failure->errors() as $error) {
                    $fail('Ligne ' . $failure->row() . ': ' . $error);
                }
            }
        }
    }
}
