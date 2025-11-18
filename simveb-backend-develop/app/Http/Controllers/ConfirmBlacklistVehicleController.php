<?php

namespace App\Http\Controllers;

use App\Models\Config\BlacklistVehicle;
use App\Repositories\BlacklistVehicleRepository;
use Illuminate\Http\Request;

class ConfirmBlacklistVehicleController extends Controller
{
    public function __construct(private readonly BlacklistVehicleRepository $repository) {
    }
    /**
     * @param
     * @param
     * @return
     */
    public function confirm(BlacklistVehicle $blacklistVehicle)
    {
        $this->authorize('validate', $blacklistVehicle);
        return response($this->repository->validate($blacklistVehicle));
    }
    public function reject(BlacklistVehicle $blacklistVehicle)
    {
        $this->authorize('reject', $blacklistVehicle);
        return response($this->repository->reject($blacklistVehicle));
    }
}
