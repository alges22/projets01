<?php

namespace App\Http\Controllers;

use App\Http\Requests\MotorcycleImportRequest;
use App\Http\Requests\MotorcycleRequest;
use App\Models\Motorcycle;
use App\Repositories\MotorcycleRepository;
use App\Services\MotorcycleService;
use Illuminate\Http\Request;

class MotorcycleController extends Controller
{
    private MotorcycleService $service;

    public function __construct(private readonly MotorcycleRepository $repository)
    {
        $this->authorizeResource(Motorcycle::class);
        $this->middleware('permission:store-motorcycle')->only(['fileFormat', 'import']);
        $this->service = new MotorcycleService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function index()
    {
        return $this->successResponse($this->repository->getAll(true, Motorcycle::relations()));
    }

    /**
     * @param MotorcycleRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(MotorcycleRequest $request)
    {
        return $this->createdResponse($this->service->store($request->validated()));
    }

    /**
     * @param Motorcycle $motorcycle
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show(Motorcycle $motorcycle)
    {
        return $this->successResponse($motorcycle->load(Motorcycle::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MotorcycleRequest $request, Motorcycle $motorcycle)
    {
        return $this->createdResponse($this->repository->update($motorcycle, $request->validated()));
    }

    /**
     * @param Motorcycle $motorcycle
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy(Motorcycle $motorcycle)
    {
        return $this->successResponse($this->repository->destroy($motorcycle));
    }

    public function fileFormat()
    {
        return $this->successResponse(public_path('format-import/motorcycle.xlsx'));
    }

    public function import(MotorcycleImportRequest $request)
    {
        return $this->createdResponse($this->repository->importMotorcycles($request->validated()));
    }
}