<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorderRequest;
use App\Models\Config\Border;
use App\Repositories\BorderRepository;
use App\Traits\CrudRepositoryTrait;

class BorderController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly BorderRepository $borderRepository)
    {
        $this->initRepository(Border::class);
        $this->authorizeResource(Border::class);
    }

    /**
     */
    public function index()
    {
        return response($this->repository->getAll(true, Border::relations()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->borderRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     * @param BorderRequest $request
     */
    public function store(BorderRequest $request)
    {
        return response($this->repository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     * @param Border $border
     */
    public function show(Border $border)
    {
        return response($border->load($border::relations()));
    }

    /**
     * Update the specified resource in storage.
     * @param BorderRequest $request
     * @param Border $border
     */
    public function update(BorderRequest $request, Border $border)
    {
        return response($this->repository->update($border, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     * @param Border $border
     */
    public function destroy(Border $border)
    {
        return response($this->repository->destroy($border));
    }
}


