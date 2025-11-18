<?php

namespace App\Http\Controllers;

use App\Services\External\AnipService;

class ExternalServiceTestController extends Controller
{
    public function getPersonFromAnip(AnipService $anipService)
    {

        return response('ok');
    }
}
