<?php
namespace Ntech\UserPackage\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserNotFoundException extends Exception
{
    protected $data;

    public function __construct($message = "Nous ne parvenons pas à retrouver votre nom d'utilisateur", $code = Response::HTTP_UNAUTHORIZED)
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
