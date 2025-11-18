<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ZoneNotFoundException extends Exception
{
    public function render()
    {
        return response("La zone d'immatriculation semble ne pas être configurée",Response::HTTP_BAD_REQUEST);
    }
}
