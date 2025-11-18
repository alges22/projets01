<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\Vehicle\VehicleTypeRequest;
use App\Models\Vehicle\VehicleType;
use App\Repositories\Vehicle\VehicleTypeRepository;
use App\Traits\CrudRepositoryTrait;

class VehicleTypeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly VehicleTypeRepository $vehicleTypeRepository)
    {
        $this->initRepository(VehicleType::class);
        $this->authorizeResource(VehicleType::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @param VehicleTypeRequest $request
     * @return Response|ResponseFactory
     */
    public function store(VehicleTypeRequest $request)
    {
        return response($this->vehicleTypeRepository->store($request->validated()));
    }

    /**
     * @param VehicleType $vehicleType
     * @return Response|ResponseFactory
     */
    public function show(VehicleType $vehicleType)
    {
        return response($vehicleType);
    }

    /**
     * @param VehicleTypeRequest $request
     * @param VehicleType $vehicletype
     * @return Response|ResponseFactory
     */
    public function update(VehicleTypeRequest $request, VehicleType $vehicleType)
    {
        return response($this->vehicleTypeRepository->update($vehicleType, $request->validated()));
    }

    /**
     * @param VehicleType $vehicleType
     * @return Response|ResponseFactory
     */
    public function destroy(VehicleType $vehicleType)
    {
        return response($this->repository->destroy($vehicleType));
    }
}
