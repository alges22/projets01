<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\VehicleRequest;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Vehicle\VehicleRepository;

class VehicleController extends Controller
{

    public function __construct(private readonly VehicleRepository $repository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->repository->create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return response($vehicle->load($vehicle::relations()));
    }

    public function getPlates(Vehicle $vehicle)
    {
        if (!$immatriculation = $vehicle->immatriculation) {
            return response([
                'message' => 'No immatriculation found for this vehicle',
            ], 404);
        }

        return response([
            'front_plate' => $immatriculation->plates()->where('plate_shape_id', $immatriculation->front_plate_shape_id)->first(),
            'back_plate' => $immatriculation->plates()->where('plate_shape_id', $immatriculation->back_plate_shape_id)->first(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return response([[
            'vehicle' => $vehicle,
        ], ...$this->repository->create()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        return response($this->repository->update($vehicle, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        return response($this->repository->destroy($vehicle));
    }
}
