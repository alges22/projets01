<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use App\Models\Action;
use App\Models\Config\Service;
use App\Repositories\ActionRepository;
use App\Traits\CrudRepositoryTrait;

class ActionController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct(private readonly ActionRepository $actionRepository)
    {
        $this->initRepository(Action::class);
        $this->authorizeResource(Action::class, 'action');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse($this->actionRepository->getAll());
    }

    public function createByService($service_id)
    {
        $service = Service::find($service_id);
        return $this->successResponse([
            'service_permissions' => $service->servicePermissions,
            'service_steps' => $service->steps
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActionRequest $request)
    {
        return $this->createdResponse($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Action $action)
    {
        return response($action->load($action::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActionRequest $request, Action $action)
    {
        return $this->createdResponse($this->repository->update($action, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Action $action)
    {
        return response($this->repository->destroy($action));
    }
}
