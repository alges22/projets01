<?php

namespace App\Http\Controllers\Admin\Config;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Services\ZonageExcelService;
use Illuminate\Http\Request;

class ZonageExcelController extends Controller
{

    public function __construct(private ZonageExcelService $service)
    {
    }

    /**
     * Display a listing of the resource to excel file.
     */
    public function export($modelName)
    {
        return $this->service->export($modelName);
    }

    /**
     * Store many created resource in storage from excel file.
     */
    public function import(Request $request, $modelName)
    {
        return response($this->service->import($request, $modelName));
    }
}
