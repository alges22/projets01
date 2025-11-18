<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagementCenterTypeRequest;
use App\Models\Config\ManagementCenterType;
use App\Traits\CrudRepositoryTrait;

class ManagementCenterTypeController extends Controller
{

    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(ManagementCenterType::class);
        $this->authorizeResource(ManagementCenterType::class);
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
     * @param ManagementCenterTypeRequest $request
     */
    public function store(ManagementCenterTypeRequest $request)
    {
        return response($this->repository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     * @param ManagementCenterType $managementCenterType
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function show(ManagementCenterType $managementCenterType)
    {
        return response($managementCenterType);
    }

    /**
     * Update the specified resource in storage.
     * @param ManagementCenterTypeRequest $request
     * @param ManagementCenterType $managementCenterType
     */
    public function update(ManagementCenterTypeRequest $request, ManagementCenterType $managementCenterType)
    {
        return response($this->repository->update($managementCenterType, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     * @param ManagementCenterType $managementCenterType
     */
    public function destroy(ManagementCenterType $managementCenterType)
    {
        return response($this->repository->destroy($managementCenterType));
    }
}
