<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alert\AlertTypeRequest;
use App\Http\Resources\AlertTypeResource;
use App\Models\Alert\AlertType;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class AlertTypeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(AlertType::class);
        $this->authorizeResource(AlertType::class);
    }

    /**
     * Display a listing of the resource
     *
     * @return ResourceCollection|ResponseFactory
     */
    public function index(): ResourceCollection
    {
        return AlertTypeResource::collection($this->repository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AlertTypeRequest $request
     * @return Response|ResponseFactory
     */
    public function store(AlertTypeRequest $request)
    {
        return response($this->repository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     *
     * @param AlertType $alertType
     * @return Response|ResponseFactory
     */
    public function show(AlertType $alertType)
    {
        return response($alertType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AlertTypeRequest $request
     * @param AlertType $alertType
     * @return Response|ResponseFactory
     */
    public function update(AlertTypeRequest $request, AlertType $alertType)
    {
        return response($this->repository->update($alertType, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlertType $alertType)
    {
        return response($this->repository->destroy($alertType));
    }
}
