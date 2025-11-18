<?php

namespace App\Http\Controllers;

use App\Http\Requests\TitleReasonTypeRequest;
use App\Models\Config\TitleReasonType;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Request;

class TitleReasonTypeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(TitleReasonType::class);
        $this->authorizeResource(TitleReasonType::class, 'title_reason_type');

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, TitleReasonType::relations()));
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
     * @param TitleReasonTypeRequest $request
     */
    public function store(TitleReasonTypeRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     * @param \App\Http\Controllers\TitleReasonType $titleReasonType
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function show(TitleReasonType $titleReasonType)
    {
        return response($titleReasonType->load(TitleReasonType::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TitleReasonType $titleReasonType)
    {

    }

    /**
     * Update the specified resource in storage.
     * @param TitleReasonTypeRequest $request
     * @param \App\Http\Controllers\TitleReasonType $titleReasonType
     */
    public function update(TitleReasonTypeRequest $request, TitleReasonType $titleReasonType)
    {
        return response($this->repository->update($titleReasonType, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Http\Controllers\TitleReasonType $titleReasonType
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function destroy(TitleReasonType $titleReasonType)
    {
        return response($this->repository->destroy($titleReasonType));
    }
}
