<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\VehicleRequest;
use App\Services\VehicleService;

class VehicleManagerController extends Controller
{

    public function __construct(private readonly VehicleService $service)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $requestData = [
            'vin' => request()->vin,
            'customs_reference' => request()->customs_ref
        ];
        $response = $this->service->showVehicleByvin($requestData);
        if (!$response['success']) {
            return $this->errorResponse($response['message'], 404);
        } else {
            return response($response['vehicle']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        return response($this->service->storeOrGetVehicleByVin($request->all()));
    }
}
