<?php

namespace App\Http\Actions;

use App\Enums\ProfileTypesEnum;
use App\Http\Requests\RejectGmdVehicleRequest;
use App\Repositories\Vehicle\GmdVehicleRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RejectGmdVehicleAction
{

    public function __invoke(RejectGmdVehicleRequest $request,GmdVehicleRepository $gmdVehicleRepository)
    {
        $authProfile = getOnlineProfile();
        if ($authProfile->type->code !== ProfileTypesEnum::gmd->name) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible d'effectuer cette action avec le profil actuel");
        }
        return response($gmdVehicleRepository->reject($request->validated()));
    }
}
