<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportGmaRequest;
use App\Repositories\Vehicle\GmaVehicleRepository;

class GmaVehicleImportExportController extends Controller
{
    public function __construct(private readonly GmaVehicleRepository $gmaVehicleRepository)
    {
    }

    /**
     * Store many created resource in storage from excel file.
     */
    public function store(ImportGmaRequest $request)
    {
        return $this->createdResponse($this->gmaVehicleRepository->importGmaVehicle($request->validated()));
    }
}
