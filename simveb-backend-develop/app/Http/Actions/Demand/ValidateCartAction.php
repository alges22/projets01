<?php

namespace App\Http\Actions\Demand;

use App\Exceptions\UnknownServiceException;
use App\Http\Requests\Demand\AddDemandToCartRequest;
use App\Services\Demand\DemandService;
use App\Traits\Demands\ValidateDemandTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidateCartAction
{
    use ValidateDemandTrait;

    /**
     * @param DemandService $service
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws UnknownServiceException
     * @throws ValidationException
     */
    public function __invoke(DemandService $service)
    {
        if (getCart()->demands()?->count() == 0){
            abort(ResponseAlias::HTTP_BAD_REQUEST, "Aucune demande dans le panier");
        }

        return response($service->validateCart());
    }
}
