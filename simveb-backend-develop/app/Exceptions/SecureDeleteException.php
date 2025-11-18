<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class SecureDeleteException extends Exception
{
    public function render()
    {
        return response(['message' => "Oups! Vous ne pouvez pas supprimer cette ressource, elle est liée à une autre entité."],Response::HTTP_FAILED_DEPENDENCY);
    }
}
