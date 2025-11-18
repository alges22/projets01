<?php

namespace App\Http\Actions;

use App\Services\PrestigeLabelImmatriculationService;
use Illuminate\Http\Request;

class SubmitPrestigeLabelImmatriculationAction
{

    public function __invoke(Request $request,PrestigeLabelImmatriculationService $service)
    {
        $request->validate(['demand_id' => ['required','exists:prestige_label_immatriculations,id']]);

        return response($service->submitDemand($request->demand_id));
    }
}
