<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportGmdRequest;
use App\Repositories\Vehicle\GmdVehicleRepository;

class GmdVehicleImportExportController extends Controller
{
    public function __construct(private readonly GmdVehicleRepository $gmdVehicleRepository)
    {
        $this->middleware('permission:import-gmd-vehicle');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImportGmdRequest $request)
    {
        return response($this->gmdVehicleRepository->import($request->validated()));
    }
}
