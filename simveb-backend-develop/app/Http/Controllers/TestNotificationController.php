<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TestNotificationController extends Controller
{

    public function index()
    {
        $this->{'test'.ucfirst(request()->type)}();

        return response(ResponseAlias::HTTP_OK);
    }
    private function testMail()
    {
        Mail::to(request()->email)->send(new TestMail);
    }

    public function testSMS()
    {

    }
}
