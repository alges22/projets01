<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleAdministrativeStatusRequest;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Repositories\VehicleAdministrativeStatusRepository;
use App\Services\VehicleAdministrativeStatusService;

class VehicleAdministrativeStatusController extends Controller
{
    public function __construct(private VehicleAdministrativeStatusService $service, private VehicleAdministrativeStatusRepository $vehicleAdministrativeStatusRepository) {}

    public function getDemandsByDeclarer($declarerId)
    {
        return response($this->service->getDeclarerDemands($declarerId, true, VehicleAdministrativeStatus::relations()));
    }

    public function store(VehicleAdministrativeStatusRequest $request)
    {
        return response($this->service->store($request));
    }

    public function show(VehicleAdministrativeStatus $vehicleAdministrativeStatus)
    {
        return response($vehicleAdministrativeStatus->load($vehicleAdministrativeStatus::relations()));
    }

    public function update(VehicleAdministrativeStatus $vehicleAdministrativeStatus, VehicleAdministrativeStatusRequest $request)
    {
        return response($this->service->update($vehicleAdministrativeStatus, $request->validated(), $request));
    }

    public function destroy(VehicleAdministrativeStatus $vehicleAdministrativeStatus)
    {
        return response($this->service->destroy($vehicleAdministrativeStatus));
    }

    public function searchDeclarant()
    {
        return response($this->vehicleAdministrativeStatusRepository->searchDeclarant());
    }

    public function searchvehicle()
    {
        return response($this->vehicleAdministrativeStatusRepository->searchVehicleAndOwner());
    }
}
