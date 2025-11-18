<?php

namespace App\Http\Controllers\Institution;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Institution\InstitutionTypeRequest;
use App\Models\Institution\InstitutionType;
use App\Traits\CrudRepositoryTrait;

class InstitutionTypeController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct()
    {
        $this->initRepository(InstitutionType::class);
        $this->authorizeResource(InstitutionType::class);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, InstitutionType::relations()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InstitutionTypeRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(InstitutionType $InstitutionType)
    {
        return response($InstitutionType->load($InstitutionType::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstitutionTypeRequest $request, InstitutionType $InstitutionType)
    {
        return response($this->repository->update($InstitutionType,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InstitutionType $InstitutionType)
    {
        return response($this->repository->destroy($InstitutionType));
    }
}
