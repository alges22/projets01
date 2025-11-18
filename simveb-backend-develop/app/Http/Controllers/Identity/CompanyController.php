<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Services\IdentityService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function __construct(private IdentityService $service) {}

    /**
     * Display the specified resource.
     */
    public function show(request $request)
    {
        $requestData = [
            'ifu' => $request->ifu
        ];
        $company = $this->service->showByIfu($requestData);

        return $company ? response($company) : response([
            'message' => "Entreprise non trouvée"
        ], 404);
    }
}
