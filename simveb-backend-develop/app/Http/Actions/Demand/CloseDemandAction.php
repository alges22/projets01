<?php

namespace App\Http\Actions\Demand;

use App\Http\Requests\Demand\CloseDemandRequest;
use App\Services\Demand\DemandService;

class CloseDemandAction
{
    public function __invoke(CloseDemandRequest $request, DemandService $service)
    {
        $service->close($request->validated());

        return response(['message' => 'Demande clôturée avec succès.']);
    }
}
