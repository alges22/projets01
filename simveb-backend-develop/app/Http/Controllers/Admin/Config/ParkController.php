<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParkRequest;
use App\Models\Config\Park;
use App\Repositories\ParkRepository;
use App\Traits\CrudRepositoryTrait;

class ParkController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly ParkRepository $parkRepository)
    {
        $this->initRepository(Park::class);
        $this->authorizeResource(Park::class);
    }

    /**
     *
     */
    public function index()
    {
        return response($this->repository->getAll(true, Park::relations()));
    }

    public function create()
    {
        return response($this->parkRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     * @param ParkRequest $request
     */
    public function store(ParkRequest $request)
    {
        return response($this->parkRepository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     * @param Park $park
     */
    public function show(Park $park)
    {
        return response($park->load($park::relations()));
    }

    /**
     * Update the specified resource in storage.
     * @param ParkRequest $request
     * @param Park $park
     */
    public function update(ParkRequest $request, Park $park)
    {
        return response($this->parkRepository->update($park, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     * @param Park $park
     */
    public function destroy(Park $park)
    {
        $park->vehicleTypes()->detach();
        $park->vehicleCategories()->detach();
        $park->towns()->detach();

        return response($this->repository->destroy($park));
    }
}
