<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceTypeRequest;
use App\Models\Config\ServiceType;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ServiceTypeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(ServiceType::class);
        $this->authorizeResource(ServiceType::class);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @param ServiceTypeRequest $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function store(ServiceTypeRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * @param ServiceType $serviceType
     * @return Response|ResponseFactory
     */
    public function show(ServiceType $serviceType)
    {
        return response($serviceType);
    }

    /**
     * @param ServiceTypeRequest $request
     * @param ServiceType $serviceType
     * @return Response|ResponseFactory
     */
    public function update(ServiceTypeRequest $request, ServiceType $serviceType)
    {
        return response($this->repository->update($serviceType, $request->validated()));
    }

    /**
     * @param ServiceType $serviceType
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function destroy(ServiceType $serviceType)
    {
        return response($this->repository->destroy($serviceType));
    }
}
