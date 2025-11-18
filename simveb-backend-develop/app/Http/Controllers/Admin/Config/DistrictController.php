<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistrictRequest;
use App\Models\Config\District;
use App\Models\Config\Town;
use App\Models\Config\Village;
use App\Services\TownDistrictVillageService;
use App\Traits\CrudRepositoryTrait;

class DistrictController extends Controller
{
    //
    use CrudRepositoryTrait;
    public function __construct(private TownDistrictVillageService $service)
    {
        $this->initRepository(District::class);
        $this->authorizeResource(District::class); //check if user has permission
    }

    public function create()
    {
        return response([
            'towns' => Town::all()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, District::relations()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DistrictRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        return response($district->load($district::relations()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(DistrictRequest $request, District $district)
    {
        return response($this->repository->update($district,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        return response($this->repository->destroy($district));
    }

    public function getVillagesForDistrict()
    {
        return response($this->service->getDataForLocation(District::class, Village::class, 'district_id'));
    }
}
