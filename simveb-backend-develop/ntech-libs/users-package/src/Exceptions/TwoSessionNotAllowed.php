<?php
namespace Ntech\UserPackage\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TwoSessionNotAllowed extends Exception
{
    protected $data;

    public function __construct($message = "Oups! Vous êtes déjà connecté sur un autre appareil.", $code = Response::HTTP_NOT_ACCEPTABLE)
    {
        parent::__construct();

        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function render(Request $request): Response
    {
        return response(["error" => true, "message" => $this->message], $this->code);
    }

}
