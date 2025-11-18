<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReimmatriculationReasonRequest;
use App\Models\Config\ReimmatriculationReason;
use App\Traits\CrudRepositoryTrait;

class ReimmatriculationReasonController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(ReimmatriculationReason::class);
        $this->authorizeResource(ReimmatriculationReason::class);
    }

    public function index()
    {
        return response($this->repository->getAll());
    }

    public function show(ReimmatriculationReason $reimmatriculationReason)
    {
        return response($reimmatriculationReason);
    }

    /**
     * @param  \App\Models\Config\ReimmatriculationReason  $reimmatriculationReason
     * @param  \App\Http\Requests\ReimmatriculationReasonRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ReimmatriculationReason $reimmatriculationReason, ReimmatriculationReasonRequest $request)
    {
        return response($this->repository->update($reimmatriculationReason, $request->validated()));
    }
}
