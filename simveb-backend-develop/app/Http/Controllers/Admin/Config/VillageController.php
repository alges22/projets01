<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\VillageRequest;
use App\Models\Config\District;
use App\Models\Config\Village;
use App\Traits\CrudRepositoryTrait;


class VillageController extends Controller
{
    //
    use CrudRepositoryTrait;
    public function __construct()
    {
        $this->initRepository(Village::class);
        $this->authorizeResource(Village::class); //check if user has permission
    }

    public function create()
    {
        return response([
            'districts' => District::all()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, Village::relations()));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(VillageRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Village $village)
    {
        return response($village->load($village::relations()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(VillageRequest $request, Village $village)
    {
        return response($this->repository->update($village,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Village $village)
    {
        return response($this->repository->destroy($village));
    }
}
