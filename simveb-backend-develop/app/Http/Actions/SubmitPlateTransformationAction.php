<?php

namespace App\Http\Actions;

use App\Services\PlateTransformationService;
use Illuminate\Http\Request;

class SubmitPlateTransformationAction
{

    public function __invoke(Request $request,PlateTransformationService $service)
    {
        $request->validate(['demand_id' => ['required',]]);

        return response($service->submitDemand($request->demand_id));
    }
}
