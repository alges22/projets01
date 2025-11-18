<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\Vehicle\VehicleCharacteristicRequest;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Repositories\Vehicle\VehicleCharacteristicRepository;
use App\Traits\CrudRepositoryTrait;

class VehicleCharacteristicController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly VehicleCharacteristicRepository $vehicleCharacteristicRepository)
    {
        $this->initRepository(VehicleCharacteristic::class);
        $this->authorizeResource(VehicleCharacteristic::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true, ['category']));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function create()
    {
        return response($this->vehicleCharacteristicRepository->getCreateData());
    }

    /**
     * @param VehicleCharacteristicRequest $request
     * @return Response|ResponseFactory
     */
    public function store(VehicleCharacteristicRequest $request)
    {
        [$success, $result] = $this->vehicleCharacteristicRepository->store($request->validated());

        return response($result, $success ? 200 : 422);
    }

    /**
     * @param VehicleCharacteristic $vehicleCharacteristic
     * @return Response|ResponseFactory
     */
    public function show(VehicleCharacteristic $vehicleCharacteristic)
    {
        return response($vehicleCharacteristic->load(VehicleCharacteristic::relations()));
    }

    /**
     * @param VehicleCharacteristicRequest $request
     * @param VehicleCharacteristic $vehicleCharacteristic
     * @return Response|ResponseFactory
     */
    public function update(VehicleCharacteristicRequest $request, VehicleCharacteristic $vehicleCharacteristic)
    {
        [$success, $result] = $this->vehicleCharacteristicRepository->update($vehicleCharacteristic, $request->validated());

        return response($result, $success ? 200 : 422);
    }

    /**
     * @param VehicleCharacteristic $vehicleCharacteristic
     * @return Response|ResponseFactory
     */
    public function destroy(VehicleCharacteristic $vehicleCharacteristic)
    {
        return response($this->repository->destroy($vehicleCharacteristic));
    }
}
