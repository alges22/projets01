<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UnknownOpertionException extends Exception
{
    public function render()
    {
        return response("Opération inconnu",Response::HTTP_BAD_REQUEST);
    }
}
