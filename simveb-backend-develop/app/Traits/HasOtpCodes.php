<?php

namespace App\Traits;

use App\Models\Auth\OtpCode;

trait HasOtpCodes
{
    public function otpCodes()
    {
        return $this->morphMany(OtpCode::class,"model","model_type");
    }

    public function otpCode()
    {
        return $this->morphOne(OtpCode::class, 'model');
    }
}
