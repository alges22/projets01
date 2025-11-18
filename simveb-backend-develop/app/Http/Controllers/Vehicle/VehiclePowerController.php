<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\VehiclePowerRequest;
use App\Models\Vehicle\VehiclePower;
use App\Traits\CrudRepositoryTrait;

class VehiclePowerController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(VehiclePower::class);
        $this->authorizeResource(VehiclePower::class);
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
    public function store(VehiclePowerRequest $request)
    {
        $result = VehiclePower::where('unit', $request->validated('unit'))
            ->where('min_value', $request->validated('min_value'))
            ->where('max_value', $request->validated('max_value'))
            ->get();
        if (count($result) > 0) {
            $data = [
                'message' => 'This vehicule power already exists.',
                'errors' => [
                    'name' => ['This vehicule power already exists.']
                ]
            ];

            return Response(['data' => $data], 422);
        }

        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(VehiclePower $vehiclePower)
    {
        return response($vehiclePower->load($vehiclePower::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehiclePower $vehiclePower)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehiclePowerRequest $request, VehiclePower $vehiclePower)
    {
        $result = VehiclePower::where('unit', $request->validated('unit'))
            ->where('min_value', $request->validated('min_value'))
            ->where('max_value', $request->validated('max_value'))
            ->get();
        if (count($result) > 0) {
            $data = [
                'message' => 'This vehicule power already exists.',
                'errors' => [
                    'name' => ['This vehicule power already exists.']
                ]
            ];

            return Response(['data' => $data], 422);
        }

        return response($this->repository->update($vehiclePower, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiclePower $vehiclePower)
    {
        return response($this->repository->destroy($vehiclePower));
    }
}
