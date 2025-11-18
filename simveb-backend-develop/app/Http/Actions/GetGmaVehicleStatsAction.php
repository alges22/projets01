<?php

namespace App\Http\Actions;

use App\Http\Requests\GmaVehicleStatsRequest;
use App\Repositories\Vehicle\GmaVehicleRepository;

class GetGmaVehicleStatsAction
{

    public function __invoke(GmaVehicleStatsRequest $request,GmaVehicleRepository $gmaVehicleRepository)
    {
        return response($gmaVehicleRepository->stats($request->validated()));
    }
}
