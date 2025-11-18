<?php

namespace App\Http\Actions\Demand;

use App\Http\Requests\Immatriculation\AssignToInterpolStaffRequest;
use App\Http\Requests\Immatriculation\AssignToStaffRequest;
use App\Services\Treatment\AssignTreatmentService;

class AssignDemandToInterpolStaffAction
{

    public function __invoke(AssignToInterpolStaffRequest $request,AssignTreatmentService $service)
    {
        return response($service->assignDemandToInterpolStaff($request->validated()));
    }
}
