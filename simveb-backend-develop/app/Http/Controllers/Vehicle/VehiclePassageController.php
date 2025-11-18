<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Immatriculation\ImmatriculationNumberRequest;
use App\Http\Requests\Vehicle\VehiclePassageRequest;
use App\Http\Resources\Vehicle\VehiclePassageCollection;
use App\Http\Resources\Vehicle\VehiclePassageResource;
use App\Models\Vehicle\VehiclePassage;
use App\Repositories\Vehicle\VehiclePassageRepository;

class VehiclePassageController extends Controller
{

    public function __construct(private readonly VehiclePassageRepository $repository)
    {
        $this->authorizeResource(VehiclePassage::class);
        $this->middleware('permission:store-vehicle-passage')->only('vehicleInfos');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse($this->repository->getAll());
    }

    /**
     *
     */
    public function vehicleInfos(ImmatriculationNumberRequest $request)
    {
        return response($this->repository->getVehicleInfosOrAlerts($request->validated('immatriculation_number')));
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
    public function store(VehiclePassageRequest $request)
    {
        return response($this->repository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     */
    public function show(VehiclePassage $vehiclePassage)
    {
        return $this->successResponse(new VehiclePassageResource($vehiclePassage->load(VehiclePassage::relations())));
    }

    /**
     *
     */
    public function edit()
    {
        return response($this->repository->create());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehiclePassageRequest $request, VehiclePassage $vehiclePassage)
    {
        return response($this->repository->update($vehiclePassage, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiclePassage $vehiclePassage)
    {
        return response($this->repository->destroy($vehiclePassage));
    }
}
