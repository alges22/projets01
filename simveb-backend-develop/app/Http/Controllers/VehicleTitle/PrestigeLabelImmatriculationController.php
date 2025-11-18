<?php

namespace App\Http\Controllers\VehicleTitle;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrestigeLabelImmatriculationRequest;
use App\Models\PrestigeLabelImmatriculation;
use App\Repositories\PrestigeLabelImmatriculationRepository;
use App\Services\PrestigeLabelImmatriculationService;

class PrestigeLabelImmatriculationController extends Controller
{

    public function __construct(private PrestigeLabelImmatriculationService $service,private PrestigeLabelImmatriculationRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, PrestigeLabelImmatriculation::relations()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrestigeLabelImmatriculationRequest $request)
    {
        return response($this->service->store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(PrestigeLabelImmatriculation $prestigeLabelImmatriculation)
    {
        return response($prestigeLabelImmatriculation->load($prestigeLabelImmatriculation::relations()));
    }

    public function update(PrestigeLabelImmatriculation $prestigeLabelImmatriculation, PrestigeLabelImmatriculationRequest $request)
    {
        return response((new PrestigeLabelImmatriculationService)->update($prestigeLabelImmatriculation, $request->validated(), $request));
    }
}
