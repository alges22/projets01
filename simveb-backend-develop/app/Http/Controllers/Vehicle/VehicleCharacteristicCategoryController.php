<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\UpdateCharacteristicCatgoryFieldNameRequest;
use App\Http\Requests\Vehicle\VehicleCharacteristicCategoryRequest;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Repositories\Vehicle\VehicleCharacteristicCategoryRepository;
use App\Services\CharacteristicCategoryService;
use App\Traits\CrudRepositoryTrait;

class VehicleCharacteristicCategoryController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly VehicleCharacteristicCategoryRepository $vehicleCharacteristicCategoryRepository, private readonly CharacteristicCategoryService $service)
    {
        $this->initRepository(VehicleCharacteristicCategory::class);
        $this->authorizeResource(VehicleCharacteristicCategory::class);
        $this->middleware('permission:update-vehicle-characteristic-category-field-name')->only(['fetchCharacteristicFields', 'updateFieldNames']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @return Response|ResponseFactory
     */
    public function create()
    {
        return response($this->vehicleCharacteristicCategoryRepository->getCreateData());
    }

    /**
     * @param VehicleCharacteristicCategoryRequest $request
     * @return Response|ResponseFactory
     */
    public function store(VehicleCharacteristicCategoryRequest $request)
    {
        return response($this->vehicleCharacteristicCategoryRepository->store($request->validated()));
    }

    /**
     * @param VehicleCharacteristicCategory $vehicleCharacteristicCategory
     * @return Response|ResponseFactory
     */
    public function show(VehicleCharacteristicCategory $vehicleCharacteristicCategory)
    {
        return response($vehicleCharacteristicCategory);
    }

    /**
     * @param VehicleCharacteristicCategoryRequest $request
     * @param VehicleCharacteristicCategory $vehicleCharacteristic
     * @return Response|ResponseFactory
     */
    public function update(VehicleCharacteristicCategoryRequest $request, VehicleCharacteristicCategory $vehicleCharacteristicCategory)
    {
        return response($this->vehicleCharacteristicCategoryRepository->update($vehicleCharacteristicCategory, $request->validated()));
    }

    /**
     * @param VehicleCharacteristicCategory $vehicleCharacteristicCategory
     * @return Response|ResponseFactory
     */
    public function destroy(VehicleCharacteristicCategory $vehicleCharacteristicCategory)
    {
        return response($this->repository->destroy($vehicleCharacteristicCategory));
    }

    public function fetchCharacteristicFields()
    {
        return $this->formatedResponse($this->service->fetchCharacteristicFields(true));
    }

    public function updateFieldNames(UpdateCharacteristicCatgoryFieldNameRequest $request)
    {
        return response($this->vehicleCharacteristicCategoryRepository->updateFieldNames($request->validated()));
    }
}
