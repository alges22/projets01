<?php

namespace App\Services\Declaration;

use App\Models\SaleDeclaration;
use App\Services\IdentityService;
use App\Services\VehicleService;
use App\Traits\CrudRepositoryTrait;

class SaleDeclarationService
{
    use CrudRepositoryTrait;

    public function __construct(private IdentityService $identityService, private VehicleService $vehicleService)
    {
        $this->initRepository(SaleDeclaration::class);
        $this->identityService = $identityService;
        $this->vehicleService = $vehicleService;
    }

    public function showByReference(array $data)
    {
        $saleDeclaration = SaleDeclaration::where(['reference' => $data['reference']])->first();

        if (!$saleDeclaration) {
            return [false, ['message' => 'Déclaration de vente introuvable']];
        }

        if ($saleDeclaration->new_owner_npi) {
            $saleDeclaration->buyer = $this->identityService->showByNpi($saleDeclaration->new_owner_npi)->response()->getData(true)['data'];
        } else {
            $saleDeclaration->buyer = $this->identityService->showByIfu([
                "ifu" => $saleDeclaration->new_owner_ifu
            ])->response()->getData(true)['data'];
        }

        if ($saleDeclaration->vehicleOwner->identity_id) {
            $saleDeclaration->owner = $this->identityService->showByNpi($saleDeclaration->vehicleOwner->identity->npi)->response()->getData(true)['data'];
        } else {
            $saleDeclaration->owner = $this->identityService->showByIfu([
                "ifu" => $saleDeclaration->vehicleOwner->space->ifu
            ])->response()->getData(true)['data'];
        }

        $saleDeclaration->sold_vehicle = $this->vehicleService->showVehicleByvin([
            "vin" => $saleDeclaration->vehicle->vin
        ])['vehicle']->response()->getData(true)['data'];

        return [true, $saleDeclaration];
    }
}
