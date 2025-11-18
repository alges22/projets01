<?php

namespace App\Http\Controllers\Admin\Config;

use App\Enums\ProfileTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Auth\Profile;
use App\Models\Config\Organization;
use App\Traits\CrudRepositoryTrait;

class OrganizationController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct()
    {
        $this->initRepository(Organization::class);
        $this->authorizeResource(Organization::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(relations: [
            'parent:id,name',
            'responsible:id,identity_id' => ['identity:id,firstname,lastname,telephone'],
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response([
            'profiles' => ProfileResource::collection(Profile::query()->whereHas('type', fn($query) => $query->where('code', ProfileTypesEnum::anatt->name))->get()),
            'organizations' => Organization::query()->select(['name','id'])->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function edit(Organization $organization)
    {
        return response([
            'profiles' => ProfileResource::collection(Profile::query()->whereHas('type', fn($query) => $query->where('code', ProfileTypesEnum::anatt->name))->get()),
            'organizations' => Organization::query()->select(['name','id'])->get(),
            'organization' => $organization
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganizationRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    public function show(Organization $organization)
    {
        return response($organization->load([
            'parent:id,name',
            'responsible:id,identity_id' => ['identity:id,firstname,lastname,telephone'],
            'staff:id,profile_id,identity_id' => ['identity:id,firstname,lastname,telephone'],
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationRequest $request, Organization $organization)
    {
        return response( $this->repository->update($organization,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        return response($this->repository->destroy($organization));
    }
}
