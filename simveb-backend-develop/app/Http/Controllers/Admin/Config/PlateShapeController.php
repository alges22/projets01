<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlateShapeRequest;
use App\Models\Plate\PlateShape;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;

class PlateShapeController extends Controller
{
    use CrudRepositoryTrait, UploadFile;

    public function __construct()
    {
        $this->initRepository(PlateShape::class);
        $this->authorizeResource(PlateShape::class); //check if user has permission
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
    public function store(PlateShapeRequest $request)
    {
        $uploadedFilePath = '';
        if (!empty($request->validated('image'))) {
            $uploadedFile = $this->saveFile($request, 'app/public/plate_shape', 'image');
            $uploadedFilePath = $uploadedFile ? $uploadedFile['path'] : '';
        }

        return response($this->repository->store([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'cost' => $request->validated('cost'),
            'image' => $uploadedFilePath,
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(PlateShape $plateShape)
    {
        return response($plateShape->load($plateShape::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlateShape $plateShape)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlateShapeRequest $request, PlateShape $plateShape)
    {
        $uploadedFilePath = '';
        if (!empty($request->validated('image'))) {
            $uploadedFile = $this->saveFile($request, 'app/public/plate_shape', 'image');
            $uploadedFilePath = $uploadedFile ? $uploadedFile['path'] : '';
        }

        return response($this->repository->update($plateShape, [
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'cost' => $request->validated('cost'),
            'image' => $uploadedFilePath,
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlateShape $plateShape)
    {
        return response($this->repository->destroy($plateShape));
    }
}
