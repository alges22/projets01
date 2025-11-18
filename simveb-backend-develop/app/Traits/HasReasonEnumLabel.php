<?php

namespace App\Traits;

use App\Enums\ReasonEnum;
use ReflectionEnumBackedCase;

trait HasReasonEnumLabel
{
    public function initializeHasReasonEnumLabel()
    {
        $this->append('reason_label');
    }

    public function getReasonEnumLabelAttribute()
    {
        if (isset($this->attributes['reason'])) {
            $reflection = new ReflectionEnumBackedCase(ReasonEnum::class, $this->attributes['reason']);

            return $reflection->getValue();
        }
        return '';
    }
}
