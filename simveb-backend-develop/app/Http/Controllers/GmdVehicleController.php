<?php

namespace App\Http\Controllers;

use App\Http\Requests\GmdVehicleRequest;
use App\Models\Vehicle\GmdVehicle;
use App\Repositories\Vehicle\GmdVehicleRepository;
use Illuminate\Http\Request;

class GmdVehicleController extends Controller
{
    public function __construct(private readonly GmdVehicleRepository $gmdVehicleRepository)
    {
        $this->authorizeResource(GmdVehicle::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->gmdVehicleRepository->getAll());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->gmdVehicleRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GmdVehicleRequest $request)
    {
        return response($this->gmdVehicleRepository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(GmdVehicle $gmdVehicle)
    {
        return response($gmdVehicle->load($gmdVehicle::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GmdVehicle $gmdVehicle)
    {
        return response($this->gmdVehicleRepository->create());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GmdVehicleRequest $request, GmdVehicle $gmdVehicle)
    {
        return response($this->gmdVehicleRepository->update($gmdVehicle, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GmdVehicle $gmdVehicle)
    {
        return response($this->gmdVehicleRepository->destroy($gmdVehicle));
    }
}
