<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\TownRequest;
use App\Models\Config\District;
use App\Models\Config\Town;
use App\Models\Config\Zone;
use App\Services\TownDistrictVillageService;
use App\Traits\CrudRepositoryTrait;

class TownController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct(private TownDistrictVillageService $service)
    {
        $this->initRepository(Town::class);
        $this->authorizeResource(Town::class); //check if user has permission
    }

    public function create()
    {
        return response([
            'zones' => Zone::all()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, Town::relations()));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TownRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Town $town)
    {
        return response($town->load($town::relations()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TownRequest $request, Town $town)
    {
        return response($this->repository->update($town,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Town $town)
    {
        return response($this->repository->destroy($town));
    }

    public function getDistrictsForTown()
    {
        return response($this->service->getDataForLocation(Town::class, District::class, 'town_id'));
    }
}
