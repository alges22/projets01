<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalStatusRequest;
use App\Models\Config\LegalStatus;
use App\Traits\CrudRepositoryTrait;

class LegalStatusController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct()
    {
        $this->initRepository(LegalStatus::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LegalStatusRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(LegalStatusRequest $request, LegalStatus $legalStatus)
    {
        return response($this->repository->update($legalStatus,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalStatus $legalStatus)
    {
        return response($this->repository->destroy($legalStatus));
    }
}
