<?php

namespace App\Http\Actions;

use App\Services\SaleDeclarationService;
use Illuminate\Http\Request;

class SubmitSaleDeclarationAction
{

    public function __invoke(Request $request,SaleDeclarationService $service)
    {
        $request->validate(['demand_id' => ['required','exists:sale_declarations,id']]);

        return response($service->submitDemand($request->demand_id));
    }
}
