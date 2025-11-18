<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\VehiclePassage;
use App\Http\Resources\Vehicle\VehiclePassageCollection;
use App\Repositories\Vehicle\VehiclePassageHistoryRepository;

class VehiclePassageHistoryController extends Controller
{
    public function __construct(private readonly VehiclePassageHistoryRepository $repository)
    {
    }

    /**
     *
     */
    public function history(string $immatriculatonNumber)
    {
        $this->authorize('viewAny', VehiclePassage::class);
        return new VehiclePassageCollection($this->repository->allPassagesOfVehicle($immatriculatonNumber));
    }

    /**
     *
     */
    public function passageHistory(VehiclePassage $vehiclePassage)
    {
        $this->authorize('viewAny', VehiclePassage::class);
        return new VehiclePassageCollection($this->repository->historyOfPassage($vehiclePassage));
    }

}
