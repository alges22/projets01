<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\OwnerTypeRequest;
use App\Models\Config\OwnerType;
use App\Repositories\OwnerTypeRepository;
use App\Traits\CrudRepositoryTrait;

class OwnerTypeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly OwnerTypeRepository $ownerTypeRepository)
    {
        $this->initRepository(OwnerType::class);
        $this->authorizeResource(OwnerType::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function edit(OwnerType $ownerType)
    {
        return response(['owner_type' => $ownerType]);
    }

    /**
     * @param OwnerTypeRequest $request
     * @return Response|ResponseFactory
     */
    public function store(OwnerTypeRequest $request)
    {
        return response($this->ownerTypeRepository->store($request->validated()));
    }

    /**
     * @param OwnerType $ownerType
     * @return Response|ResponseFactory
     */
    public function show(OwnerType $ownerType)
    {
        return response($ownerType->load(OwnerType::relations()));
    }

    /**
     * @param OwnerTypeRequest $request
     * @param OwnerType $ownerType
     * @return Response|ResponseFactory
     */
    public function update(OwnerTypeRequest $request, OwnerType $ownerType)
    {
        return response($this->ownerTypeRepository->update($ownerType, $request->validated()));
    }

    /**
     * @param OwnerType $ownerType
     * @return Response|ResponseFactory
     */
    public function destroy(OwnerType $ownerType)
    {
        return response($this->repository->destroy($ownerType));
    }
}
