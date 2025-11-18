<?php

namespace App\Http\Actions;

use App\Http\Requests\Immatriculation\ValidateTreatmentRequest;
use App\Services\Treatment\ValidateTreatmentService;

class ValidateTreatmentAction
{

    public function __invoke(ValidateTreatmentRequest $request,ValidateTreatmentService $service)
    {
        return response($service->validateTreatment($request->validated()));
    }
}
