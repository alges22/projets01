<?php

namespace App\Http\Actions;

use App\Enums\ProfileTypesEnum;
use App\Http\Requests\ValidateGmaVehicleRequest;
use App\Repositories\Vehicle\GmaVehicleRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidateGmaVehicleAction
{

    public function __invoke(ValidateGmaVehicleRequest $request,GmaVehicleRepository $gmaVehicleRepository)
    {
        return response($gmaVehicleRepository->validate($request->validated()));
    }
}
