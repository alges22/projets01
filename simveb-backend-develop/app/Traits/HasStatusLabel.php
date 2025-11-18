<?php

namespace App\Traits;

use App\Enums\Status;

trait HasStatusLabel
{
    public function initializeHasStatusLabel()
    {
        $this->append('status_label');
    }

    public function getStatusLabelAttribute()
    {
        return getModelAttributeLabelFromEnum($this, 'status', Status::class);
    }
}
