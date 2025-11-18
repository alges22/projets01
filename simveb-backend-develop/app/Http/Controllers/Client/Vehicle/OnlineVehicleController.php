<?php

namespace App\Http\Controllers\Client\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientVehicleCollection;
use App\Repositories\Vehicle\VehicleRepository;

class OnlineVehicleController extends Controller
{
    public function __construct(private readonly VehicleRepository $repository) {}

    public function index()
    {
        return response(new ClientVehicleCollection($this->repository->getClientVehicles()));
    }
}
