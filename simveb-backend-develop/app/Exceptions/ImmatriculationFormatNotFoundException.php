<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImmatriculationFormatNotFoundException extends Exception
{
    public function render()
    {
        return response("Immatriculation format not found", ResponseAlias::HTTP_BAD_REQUEST);
    }
}
