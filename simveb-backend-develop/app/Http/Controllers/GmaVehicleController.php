<?php

namespace App\Http\Controllers;

use App\Http\Requests\GmaVehicleRequest;
use App\Models\Vehicle\GmaVehicle;
use App\Repositories\Vehicle\GmaVehicleRepository;

class GmaVehicleController extends Controller
{
    public function __construct(private readonly GmaVehicleRepository $gmaVehicleRepository)
    {
        $this->authorizeResource(GmaVehicle::class,'gma_vehicle');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse($this->gmaVehicleRepository->getAll());
    }

    public function create()
    {
        return [
            'import_model' => file_exists(public_path('format-import/gma_vehicle_template.xlsx')) ? asset('format-import/gma_vehicle_template.xlsx') : "",
            'institutions' => $this->gmaVehicleRepository->create(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GmaVehicleRequest $request)
    {
        return $this->createdResponse($this->gmaVehicleRepository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(GmaVehicle $gmaVehicle)
    {
        return response($gmaVehicle->load(GmaVehicle::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GmaVehicleRequest $request, GmaVehicle $gmaVehicle)
    {
        return $this->createdResponse($this->gmaVehicleRepository->update($gmaVehicle, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GmaVehicle $gmaVehicle)
    {
        return response($this->gmaVehicleRepository->destroy($gmaVehicle));
    }
}
