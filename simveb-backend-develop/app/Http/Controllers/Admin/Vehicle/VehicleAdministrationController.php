<?php

namespace App\Http\Controllers\Admin\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleAdministrationRequest;
use App\Repositories\Vehicle\VehicleRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class VehicleAdministrationController extends Controller
{
    public function __construct(private readonly VehicleRepository $vehicleRepository)
    {
        $this->middleware('permission:show-vehicle')->only(['show']);
    }

    public function show(VehicleAdministrationRequest $request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return response($this->vehicleRepository->showDetails($request->validated()));
    }

    public function affiliateShow(VehicleAdministrationRequest $request)
    {
        return response($this->vehicleRepository->showDetails($request->validated(), true));
    }
}
