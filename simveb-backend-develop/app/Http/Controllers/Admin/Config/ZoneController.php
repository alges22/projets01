<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ZoneRequest;
use App\Models\Config\Zone;
use App\Repositories\ZoneRepository;
use App\Traits\CrudRepositoryTrait;

class ZoneController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly ZoneRepository $zoneRepository)
    {
        $this->initRepository(Zone::class);
        $this->authorizeResource(Zone::class); //check if user has permission
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, Zone::relations()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->zoneRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ZoneRequest $request)
    {
        return response($this->zoneRepository->store($request->validated(),$request));
    }

    /**
     * Display the specified resource.
     * @param Zone $zone
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function show(Zone $zone)
    {
        return response($zone->load($zone::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     * @param Zone $zone
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function edit(Zone $zone)
    {
        return response($this->zoneRepository->edit($zone));
    }

    /**
     * Update the specified resource in storage.
     * @param ZoneRequest $request
     * @param Zone $zone
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function update(ZoneRequest $request, Zone $zone)
    {
        return response($this->zoneRepository->update($zone, $request->validated(), $request));

    }

    /**
     * Remove the specified resource from storage.
     * @param Zone $zone
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function destroy(Zone $zone)
    {
        $zone->towns()->detach();
        return response($this->repository->destroy($zone));
    }
}
