<?php

namespace App\Traits;

use App\Enums\Code;
use ReflectionEnumBackedCase;

trait HasCodeLabel
{
    public function initializeHasCodeLabel()
    {
        $this->append('code_label');
    }

    public function getCodeLabelAttribute()
    {
        if (isset($this->attributes['code'])) {
            $reflection = new ReflectionEnumBackedCase(Code::class, $this->attributes['code']);

            return $reflection->getValue();
        }
        return '';
    }
}
