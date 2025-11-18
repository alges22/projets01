<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\VehicleBrandRequest;
use App\Models\Vehicle\VehicleBrand;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;

class VehicleBrandController extends Controller
{
    use CrudRepositoryTrait, UploadFile;

    public function __construct()
    {
        $this->initRepository(VehicleBrand::class);
        $this->authorizeResource(VehicleBrand::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleBrandRequest $request)
    {
        $uploadedFilePath = '';
        if (!empty($request->validated('image'))) {
            $uploadedFile = $this->saveFile($request, 'app/public/plate_shape', 'image');
            $uploadedFilePath = $uploadedFile ? $uploadedFile['path'] : '';
        }

        return response($this->repository->store([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'native_country' => $request->validated('native_country'),
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleBrand $vehicleBrand)
    {
        return response($vehicleBrand->load($vehicleBrand::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleBrand $vehicleBrand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleBrandRequest $request, VehicleBrand $vehicleBrand)
    {
        return response($this->repository->update($vehicleBrand, [
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'native_country' => $request->validated('native_country'),
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleBrand $vehicleBrand)
    {
        return response($this->repository->destroy($vehicleBrand));
    }
}
