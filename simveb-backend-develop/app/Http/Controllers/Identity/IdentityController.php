<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Services\IdentityService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IdentityController extends Controller
{

    public function __construct(private IdentityService $service)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $response = $this->service->showByNpi($request->npi);
        if (!isset($response->response()->getData(true)['data']['npi'])) {
            return $this->errorResponse('Personne introuvable.', ResponseAlias::HTTP_NOT_FOUND);
        } else {
            return response($response);
        }
    }

    public function getIdentities(string $npis)
    {
        return $this->service->getIdentites($npis);
    }
}
