<?php

namespace App\Http\Actions;

use App\Http\Requests\Immatriculation\SuspendTreatmentRequest;
use App\Services\Treatment\TreatmentService;

class SuspendTreatmentAction
{

    public function __invoke(SuspendTreatmentRequest $request,TreatmentService $service)
    {
        return response($service->suspendTreatment($request->validated()));
    }
}
