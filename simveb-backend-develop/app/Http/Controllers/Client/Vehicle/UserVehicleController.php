<?php

namespace App\Http\Controllers\Client\Vehicle;

use App\Http\Controllers\Controller;
use App\Repositories\Vehicle\VehicleOwnerRepository;

class UserVehicleController extends Controller
{
    public function __construct(private readonly VehicleOwnerRepository $repository) {}

    public function index()
    {
        return response($this->repository->getClientVehicles());
    }
}
