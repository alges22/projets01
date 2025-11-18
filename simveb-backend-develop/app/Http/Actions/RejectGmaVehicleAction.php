<?php

namespace App\Http\Actions;

use App\Enums\ProfileTypesEnum;
use App\Http\Requests\RejectGmaVehicleRequest;
use App\Repositories\Vehicle\GmaVehicleRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RejectGmaVehicleAction
{

    public function __invoke(RejectGmaVehicleRequest $request,GmaVehicleRepository $gmaVehicleRepository)
    {
        $authProfile = getOnlineProfile();
        if ($authProfile->type->code !== ProfileTypesEnum::gma->name) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible d'effectuer cette action avec le profil actuel");
        }
        return response($gmaVehicleRepository->reject($request->validated()));
    }
}
