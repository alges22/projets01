<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\VehicleAlertRequest;
use App\Http\Resources\Vehicle\VehicleAlertCollection;
use App\Http\Resources\Vehicle\VehicleAlertResource;
use App\Models\Vehicle\VehicleAlert;
use App\Repositories\Vehicle\VehicleAlertRepository;

class VehicleAlertController extends Controller
{

    public function __construct(private readonly VehicleAlertRepository $repository)
    {
        $this->authorizeResource(VehicleAlert::class);
    }

    /**
     *
     */
    public function index()
    {
        return new VehicleAlertCollection($this->repository->getAll());
    }

    /**
     *
     */
    public function create()
    {
        return response($this->repository->create());
    }

    /**
     *
     */
    public function store(VehicleAlertRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     *
     */
    public function show(VehicleAlert $vehicleAlert)
    {
        return new VehicleAlertResource($vehicleAlert->load(VehicleAlert::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleAlert $vehicleAlert)
    {
        return response($this->repository->create());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleAlertRequest $request, VehicleAlert $vehicleAlert)
    {
        return response($this->repository->update($vehicleAlert, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleAlert $vehicleAlert)
    {
        return response($this->repository->destroy($vehicleAlert));
    }
}
