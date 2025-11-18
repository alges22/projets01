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

class AddDemandToCartAction
{
    use ValidateDemandTrait;

    /**
     * @param AddDemandToCartRequest $request
     * @param DemandService $service
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws ValidationException
     * @throws UnknownServiceException
     */
    public function __invoke(AddDemandToCartRequest $request, DemandService $service)
    {
        $demand = $service->store($request->validated(), $request);

        return response($service->addDemandToCart($demand));
    }
}
