<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotificationConfigNotFoundException extends Exception
{
    public function render()
    {
        return response("Notification config is not set",Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
