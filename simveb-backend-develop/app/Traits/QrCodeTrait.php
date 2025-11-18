<?php

namespace App\Traits;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

trait QrCodeTrait
{
    use UploadFile;

    protected function generateQrCode($value): string
    {
        $this->createFolder(public_path("storage/qr_codes"));

        $fileName = time() . '-' . Str::random();
        $filePath = "storage/qr_codes/{$fileName}.svg";

        QrCode::generate($value, public_path($filePath));

        return $filePath;
    }
}
