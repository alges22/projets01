<?php

namespace App\Http\Controllers;

use App\Repositories\Vehicle\GoVehicleRepository;
use Illuminate\Http\Request;

class GovVehicleImportExportController extends Controller
{
    public function __construct(private readonly GoVehicleRepository $goVehicleRepository)
    {
    }

    /**
     * Store many created resource in storage from excel file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        return $this->createdResponse($this->goVehicleRepository->importGovVehicle($request));
    }
}
