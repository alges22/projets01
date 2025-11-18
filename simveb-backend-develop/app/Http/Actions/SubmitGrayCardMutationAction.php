<?php

namespace App\Http\Actions;

use App\Services\GrayCardMutationService;
use Illuminate\Http\Request;

class SubmitGrayCardMutationAction
{

    public function __invoke(Request $request,GrayCardMutationService $service)
    {
        $request->validate(['demand_id' => ['required','exists:gray_card_mutations,id']]);

        return response($service->submitDemand($request->demand_id));
    }
}
