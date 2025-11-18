<?php

namespace App\Http\Actions;

use App\Services\Demand\DemandService;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApproveCartAction
{

    public function __invoke(DemandService $demandService)
    {
        
        return response($demandService->approveCart());
    }
}
