<?php

namespace App\Rules;

use App\Models\Config\Service;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

class DocumentRequiredRule implements ValidationRule
{
    public function __construct(private readonly Service $service)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $documentIds = [];
        foreach ($value as $item){
            $documentIds[] = $item['type_id'];
        }

        if (array_diff($documentIds, $this->service->documents()->pluck('id')->toArray())){
            $fail("Tout les documents requis pour ce service ne sont pas téléversés.");
        }
    }
}
