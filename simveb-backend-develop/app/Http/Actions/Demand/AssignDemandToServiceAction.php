<?php

namespace App\Http\Actions\Demand;

use App\Http\Requests\Immatriculation\AssignToServiceRequest;
use App\Models\Order\Demand;
use App\Services\Treatment\AssignTreatmentService;

class AssignDemandToServiceAction
{

    public function __invoke(AssignToServiceRequest $request,AssignTreatmentService $service)
    {
        $demand = Demand::findOrFail($request->demand_id);

        return response($service->assignDemandToService($demand, $request->organization_id));
    }
}
