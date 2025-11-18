<?php

namespace App\Http\Controllers\Admin\Demand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Demand\ValidateOrRejectDemandUpdatesRequest;
use App\Repositories\Demand\DemandUpdatesHistoryRepository;
use Illuminate\Http\Request;

class DemandUpdatesHistoryController extends Controller
{
    public function __construct(private readonly DemandUpdatesHistoryRepository $repository)
    {
        //
    }

    /**
     *
     */
    public function validateAll(ValidateOrRejectDemandUpdatesRequest $request)
    {
        return response($this->repository->validate($request->validated()));
    }
}
