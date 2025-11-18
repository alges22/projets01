<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\Vehicle\VehicleCategoryRequest;
use App\Models\Vehicle\VehicleCategory;
use App\Repositories\Vehicle\VehicleCategoryRepository;
use App\Traits\CrudRepositoryTrait;

class VehicleCategoryController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly VehicleCategoryRepository $vehicleCategoryRepository)
    {
        $this->initRepository(VehicleCategory::class);
        $this->authorizeResource(VehicleCategory::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @param VehicleCategoryRequest $request
     * @return Response|ResponseFactory
     */
    public function store(VehicleCategoryRequest $request)
    {
        return response($this->vehicleCategoryRepository->store($request->validated()));
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @return Response|ResponseFactory
     */
    public function show(VehicleCategory $vehicleCategory)
    {
        return response($vehicleCategory);
    }

    /**
     * @param VehicleCategoryRequest $request
     * @param VehicleCategory $vehicleCategory
     * @return Response|ResponseFactory
     */
    public function update(VehicleCategoryRequest $request, VehicleCategory $vehicleCategory)
    {
        return response($this->vehicleCategoryRepository->update($vehicleCategory, $request->validated()));
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @return Response|ResponseFactory
     */
    public function destroy(VehicleCategory $vehicleCategory)
    {
        return response($this->repository->destroy($vehicleCategory));
    }
}
