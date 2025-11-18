<?php

namespace App\Http\Controllers\Admin\Treatment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlateTransformationRequest;
use App\Models\PlateTransformation;
use App\Repositories\PlateTransformationRepository;
use App\Services\PlateTransformationService;

class PlateTransformationController extends Controller
{

    public function __construct(private PlateTransformationService $service, private PlateTransformationRepository $repository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, PlateTransformation::relations()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlateTransformationRequest $request)
    {
        return response($this->service->store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(PlateTransformation $plateTransformation)
    {
        return response($plateTransformation->load($plateTransformation::relations()));
    }

    public function update(PlateTransformation $plateTransformation, PlateTransformationRequest $request)
    {
        return response((new PlateTransformationService)->update($plateTransformation, $request->validated(), $request));
    }
}
