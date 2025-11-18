<?php

namespace App\Services;

use App\Traits\UploadFile;
use Illuminate\Http\UploadedFile;

class VehiclePassageService
{
    use UploadFile;



    /**
     *
     */
    public function getStoredFile($validatedFile, $folder): mixed
    {
        if ($validatedFile instanceof UploadedFile) {
            $storedFile = $this->saveFile($validatedFile, $folder);
        } else {
            $storedFile = $this->saveBase64File($validatedFile, $folder);
        }

        return $storedFile;
    }
}
