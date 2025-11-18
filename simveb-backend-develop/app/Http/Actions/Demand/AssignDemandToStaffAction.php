<?php

namespace App\Http\Actions\Demand;

use App\Http\Requests\Immatriculation\AssignToStaffRequest;
use App\Models\Auth\Profile;
use App\Models\Order\Demand;
use App\Services\Treatment\AssignTreatmentService;

class AssignDemandToStaffAction
{

    public function __invoke(AssignToStaffRequest $request,AssignTreatmentService $service)
    {
        $demand = Demand::find($request->demand_id);
        $staff = Profile::query()
        ->whereHas('identity', fn($query) => $query->where('npi', $request->npi))
        ->first();

        if ($staff){
            $result = $service->assignDemandToStaff($demand, $staff);
        }else{
            $result = $service->autoAssignToStaff($demand);
        }
        return response($result);
    }
}
