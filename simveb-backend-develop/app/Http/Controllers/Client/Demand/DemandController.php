<?php

namespace App\Http\Controllers\Client\Demand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Demand\DemandRequest;
use App\Http\Requests\Demand\UpdateDemandRequest;
use App\Http\Resources\ClientDemandResource;
use App\Models\Config\Service;
use App\Models\Order\Demand;
use App\Repositories\Demand\DemandRepository;
use App\Services\Demand\CreateDataService;
use App\Services\Demand\DemandService;
use App\Services\VehicleService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class DemandController extends Controller
{
    public function __construct(
        private DemandRepository $repository,
        private DemandService $service,
        private readonly CreateDataService $createDataService
    )
    {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->getClientDemands();
    }

    /**
     * @param Service $service
     * @return \Illuminate\Foundation\Application|Response|Application|ResponseFactory
     */
    public function create(Service $service): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return response($this->createDataService->getCreateData($service));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DemandRequest $request)
    {
        return response($this->service->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Demand $demand)
    {
        return response(new ClientDemandResource($demand));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Demand $demand)
    {
        return response($this->createDataService->getEditData($demand));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDemandRequest $request, Demand $demand)
    {
        return response($this->service->update($demand, $request->validated(), $request));
    }

    public function verifyVehicleSituation(string $vin)
    {
        [$success, $result] = (new VehicleService)->verifyVehicleSituation($vin);

        return response($result, $success ? 200 : 404);
    }
}
