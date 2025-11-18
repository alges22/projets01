<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCharacteristicRequest;
use App\Http\Requests\Demand\VehicleTransformationRequest;
use App\Models\TransformationCharacteristic;
use App\Models\VehicleTransformation;
use App\Services\VehicleTransformation\VehicleTransformationServiceAdapter;
use Illuminate\Http\Request;

class VehicleTransformationController extends Controller
{

    private $serviceAdapter;

    public function __construct(VehicleTransformationServiceAdapter $serviceAdapter)
    {
        $this->serviceAdapter = $serviceAdapter;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddCharacteristicRequest $request)
    {
        return response($this->serviceAdapter->transformationCharacteristic($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleTransformation $vehicleTransformation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleTransformation $vehicleTransformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleTransformation $vehicleTransformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleTransformation $vehicleTransformation, $id)
    {
        return response($this->serviceAdapter->delete($id));
    }
}
