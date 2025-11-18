<?php

namespace App\Http\Actions\Demand;

use App\Http\Requests\Immatriculation\AssignToCenterRequest;
use App\Models\Order\Demand;
use App\Services\Treatment\AssignTreatmentService;

class AssignDemandToCenterAction
{

    public function __invoke(AssignToCenterRequest $request, AssignTreatmentService $service)
    {
        $demand = Demand::findOrFail($request->demand_id);

        return response($service->assignDemandToCenter($demand, $request->center_id));
    }
}
