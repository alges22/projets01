<?php

namespace App\Http\Actions;

use App\Http\Requests\Immatriculation\VerifyTreatmentRequest;
use App\Services\Treatment\TreatmentService;

class VerifyDemandAction
{

    public function __invoke(VerifyTreatmentRequest $request,TreatmentService $service)
    {
        return response($service->verifyTreatment($request->validated()));
    }
}
