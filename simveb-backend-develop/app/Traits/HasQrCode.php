<?php

namespace App\Traits;

trait HasQrCode
{
    use QrCodeTrait;

    public function initializeQrCodeTrait()
    {
        $this->append('qr_code');
    }

    public function getQrCodeAttribute()
    {
        if (isset($this->attributes['qr_code_path'])) {
            return asset($this->attributes['qr_code_path']);
        }
        return '';
    }
}
