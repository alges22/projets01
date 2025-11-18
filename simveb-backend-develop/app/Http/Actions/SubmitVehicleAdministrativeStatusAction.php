<?php

namespace App\Http\Actions;

use App\Services\VehicleAdministrativeStatusService;
use Illuminate\Http\Request;

class SubmitVehicleAdministrativeStatusAction
{

    public function __invoke(Request $request,VehicleAdministrativeStatusService $service)
    {
        $request->validate(['demand_id' => ['required','exists:vehicle_administrative_statuses,id']]);

        return response($service->submitDemand($request->demand_id));
    }
}
