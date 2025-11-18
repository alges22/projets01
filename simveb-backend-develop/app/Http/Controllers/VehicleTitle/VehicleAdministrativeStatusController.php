<?php

namespace App\Http\Controllers\VehicleTitle;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleAdministrativeStatusRequest;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Repositories\VehicleAdministrativeStatusRepository;
use App\Services\VehicleAdministrativeStatusService;

class VehicleAdministrativeStatusController extends Controller
{
    public function __construct(private VehicleAdministrativeStatusRepository $repository, private VehicleAdministrativeStatusService $service)
    {
        $this->authorizeResource(VehicleAdministrativeStatus::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, VehicleAdministrativeStatus::relations()));
    }

    /**
     * Store a newly created resource in storage.
     * @param vehicleAdministrativeStatusRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function store(VehicleAdministrativeStatusRequest $request)
    {
        //
    }

    public function show(VehicleAdministrativeStatus $vehicleAdministrativeStatus)
    {
        return response($vehicleAdministrativeStatus->load($vehicleAdministrativeStatus::relations()));
    }
}
