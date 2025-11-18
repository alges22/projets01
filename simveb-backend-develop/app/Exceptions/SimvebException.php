<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class SimvebException extends Exception
{
    public function __construct(string $message = "", int $code = Response::HTTP_BAD_REQUEST, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response($this->message,$this->code);
    }
}
