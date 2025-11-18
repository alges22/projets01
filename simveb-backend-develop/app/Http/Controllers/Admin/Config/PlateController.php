<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plate\PlateRequest;
use App\Models\Plate;
use App\Repositories\Plate\PlateRepository;

class PlateController extends Controller
{
    public function __construct(private readonly PlateRepository $plateRepository)
    {
        $this->authorizeResource(Plate::class);
        $this->middleware('permission:stats-create')->only('stats');
    }

    public function index()
    {
        return response($this->plateRepository->getAll(true, Plate::relations()));
    }

    public function show(Plate $plate)
    {
        return response($plate->load($plate::relations()));
    }

    public function create()
    {
        return response(file_exists(public_path('format-import/format_import_plate.xlsx')) ? asset('format-import/format_import_plate.xlsx') : "");
    }

    public function store(PlateRequest $request)
    {
        return response($this->plateRepository->store($request->validated()));
    }

    public function stats()
    {
        return response($this->plateRepository->stats());
    }
}
