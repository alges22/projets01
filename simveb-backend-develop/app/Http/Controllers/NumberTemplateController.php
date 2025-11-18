<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberTemplateRequest;
use App\Models\Config\NumberTemplate;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Request;

class NumberTemplateController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(NumberTemplate::class);
        $this->authorizeResource(NumberTemplate::class,'number_template');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse($this->repository->getAll());
    }

    public function show(NumberTemplate $numberTemplate)
    {
        return response($numberTemplate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NumberTemplateRequest $request)
    {
        return $this->successResponse($this->repository->store($request->validated()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(NumberTemplateRequest $request, NumberTemplate $numberTemplate)
    {
        return $this->successResponse($this->repository->update($numberTemplate, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NumberTemplate $numberTemplate)
    {
        return $this->successResponse($this->repository->destroy($numberTemplate));
    }
}
