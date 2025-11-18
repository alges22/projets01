<?php

namespace App\Http\Controllers\Client\Demand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Demand\DemandOtpRequest;
use App\Http\Requests\Demand\VerifyDemandOtpRequest;
use App\Repositories\Demand\DemandOtpRepository;

class DemandOtpController extends Controller
{
    public function __construct(
        private DemandOtpRepository $repository,
    ) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(DemandOtpRequest $request)
    {
        return response($this->repository->store($request->validated(), $request));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VerifyDemandOtpRequest $request)
    {
        return response($this->repository->update($request->validated()));
    }
}
