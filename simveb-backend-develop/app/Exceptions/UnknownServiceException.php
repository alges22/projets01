<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UnknownServiceException extends Exception
{
    public function render()
    {
        return response("Oups! Ce service n'existe pas", ResponseAlias::HTTP_BAD_REQUEST);
    }
}
