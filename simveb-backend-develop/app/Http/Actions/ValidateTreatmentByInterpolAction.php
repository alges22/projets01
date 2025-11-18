<?php

namespace App\Http\Actions;

use App\Http\Requests\Immatriculation\ValidateTreatmentRequest;
use App\Services\Treatment\TreatmentService;

class ValidateTreatmentByInterpolAction
{

    public function __invoke(ValidateTreatmentRequest $request,TreatmentService $service)
    {
        return response($service->validateTreatmentByInterpol($request->validated()));
    }
}
