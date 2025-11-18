<?php

namespace App\Http\Controllers\Immatriculation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Immatriculation\ImmatriculationFormatRequest;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Services\Immatriculation\ImmatriculationFormatService;

class ImmatriculationFormatController extends Controller
{

    public function __construct(private readonly ImmatriculationFormatService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->service->repository->getAll(relations: [
            'vehicleCategory:id,name',
            'components:id,description'
        ]));
    }

    public function show(ImmatriculationFormat $immatriculationFormat)
    {
        return response($immatriculationFormat->load($immatriculationFormat::relations()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->service->create());
    }
    /**
     * Show the form for creating a new resource.
     */
    public function edit(ImmatriculationFormat $immatriculationFormat)
    {
        return response([
            'immatriculation_format' => $immatriculationFormat->load(['components'])
        ] + $this->service->create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImmatriculationFormatRequest $request)
    {
        return response($this->service->store($request->validated()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ImmatriculationFormatRequest $request, ImmatriculationFormat $immatriculationFormat)
    {
        return response($this->service->update($immatriculationFormat, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImmatriculationFormat $immatriculationFormat)
    {
        $immatriculationFormat->components()->detach();

        return response($this->service->repository->destroy($immatriculationFormat));
    }
}
