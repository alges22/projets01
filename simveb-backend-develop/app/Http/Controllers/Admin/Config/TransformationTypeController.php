<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransformationTypeRequest;
use App\Models\Config\TransformationType;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Request;

class TransformationTypeController extends Controller
{

    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(TransformationType::class);
        $this->authorizeResource(TransformationType::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, TransformationType::relations()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransformationTypeRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(TransformationType $transformationType)
    {
        return response($transformationType->load(TransformationType::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransformationType $transformationType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransformationTypeRequest $request, TransformationType $type)
    {
        return response($this->repository->update($type, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransformationType $type)
    {
        return response($this->repository->destroy($type));
    }
}
