<?php

namespace App\Http\Actions\Demand;

use App\Http\Requests\Immatriculation\AssignToInterpolRequest;
use App\Services\Treatment\AssignTreatmentService;

class AssignDemandToInterpolAction
{

    public function __invoke(AssignToInterpolRequest $request,AssignTreatmentService $service)
    {
        return response($service->affectDemandToInterpol($request->validated()));
    }
}
