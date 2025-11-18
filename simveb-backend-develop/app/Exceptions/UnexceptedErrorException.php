<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UnexceptedErrorException extends Exception
{
    public function render()
    {
        return response()->json(['message' => __('error.unexpected')], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
