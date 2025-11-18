<?php

namespace Ntech\NotifierPackage\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotifierChannelNotFoundException extends Exception
{
    public function __construct(private readonly string $type)
    {
        parent::__construct();
    }

    public function render()
    {
        return response("Worklow type {$this->type} doesn't exist.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
