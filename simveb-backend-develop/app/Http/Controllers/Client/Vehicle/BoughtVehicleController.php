<?php

namespace App\Http\Controllers\Client\Vehicle;

use App\Http\Controllers\Controller;
use App\Repositories\Vehicle\VehicleRepository;

class BoughtVehicleController extends Controller
{
    public function __construct(private readonly VehicleRepository $repository) {}

    public function index()
    {
        return response($this->repository->getClientBoughtVehicles());
    }
}
