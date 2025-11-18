<?php

namespace App\Http\Actions;

use App\Http\Requests\Immatriculation\PrintImmatriculationRequest;
use App\Services\Treatment\TreatmentService;

class PrintImmatriculationAction
{

    public function __invoke(PrintImmatriculationRequest $request,TreatmentService $service)
    {
        return response($service->printImmatriculation($request->validated()));
    }
}
