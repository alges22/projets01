<?php

namespace App\Http\Actions;

use App\Http\Requests\ValidateGmdVehicleRequest;
use App\Repositories\Vehicle\GmdVehicleRepository;

class ValidateGmdVehicleAction
{
    public function __construct(private readonly GmdVehicleRepository $gmdVehicleRepository)
    {
    }

    /**
     *
     */
    public function __invoke(ValidateGmdVehicleRequest $request)
    {
        return response($this->gmdVehicleRepository->validate($request->validated()));
    }
}
