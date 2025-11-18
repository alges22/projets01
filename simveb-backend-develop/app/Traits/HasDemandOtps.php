<?php

namespace App\Traits;

use App\Models\DemandOtp;

trait HasDemandOtps
{
    public function demandOtps()
    {
        return $this->morphMany(DemandOtp::class,"model","model_type");
    }

    public function demandOtp()
    {
        return $this->morphOne(DemandOtp::class, 'model');
    }
}
