<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\VehicleEnergySourceRequest;
use App\Models\Vehicle\VehicleEnergySource;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;

class VehicleEnergySourceController extends Controller
{
    use CrudRepositoryTrait, UploadFile;

    public function __construct()
    {
        $this->initRepository(VehicleEnergySource::class);
        $this->authorizeResource(VehicleEnergySource::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleEnergySourceRequest $request)
    {
        return response($this->repository->store([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleEnergySource $vehicleEnergySource)
    {
        return response($vehicleEnergySource->load($vehicleEnergySource::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleEnergySource $vehicleEnergySource)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleEnergySourceRequest $request, VehicleEnergySource $vehicleEnergySource)
    {
        return response($this->repository->update($vehicleEnergySource, [
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleEnergySource $vehicleEnergySource)
    {
        return response($this->repository->destroy($vehicleEnergySource));
    }
}
