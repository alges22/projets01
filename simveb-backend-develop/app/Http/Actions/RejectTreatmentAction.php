<?php

namespace App\Http\Actions;

use App\Http\Requests\Immatriculation\RejectTreatmentRequest;
use App\Services\Treatment\TreatmentService;

class RejectTreatmentAction
{

    public function __invoke(RejectTreatmentRequest $request,TreatmentService $service)
    {
        return response($service->rejectTreatment($request->validated()));
    }
}
