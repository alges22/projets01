<?php

namespace App\Http\Controllers\Space;

use App\Http\Controllers\Controller;
use App\Http\Requests\Space\SpaceSuspensionRequestFormRequest;
use App\Http\Requests\Space\ValidateOrRejectSpaceSuspensionRequest;
use App\Models\Space\SpaceSuspensionRequest;
use App\Repositories\Space\SpaceSuspensionRequestRepository;
use App\Traits\CrudRepositoryTrait;

class SpaceSuspensionRequestController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly SpaceSuspensionRequestRepository $suspensionRepository)
    {
        $this->initRepository(SpaceSuspensionRequest::class);
        $this->authorizeResource(SpaceSuspensionRequest::class);
        $this->middleware('permission:validate-space-suspension-request')->only(['validateOrReject']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true, SpaceSuspensionRequest::relations()));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function show(SpaceSuspensionRequest $spaceSuspensionRequest)
    {
        return response($spaceSuspensionRequest->load($spaceSuspensionRequest::relations()));
    }

    public function create()
    {
        return response($this->suspensionRepository->createData());
    }

    public function store(SpaceSuspensionRequestFormRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = getOnlineProfile()->id;

        return response($this->repository->store($data));
    }

    public function edit(SpaceSuspensionRequest $spaceSuspensionRequest)
    {
        return response(array_merge(['space_suspension_request' => $spaceSuspensionRequest], $this->suspensionRepository->createData()));
    }

    public function update(SpaceSuspensionRequestFormRequest $request, SpaceSuspensionRequest $spaceSuspensionRequest)
    {
        return response($this->repository->update($spaceSuspensionRequest, $request->validated()));
    }

    public function validateOrReject(spaceSuspensionRequest $spaceSuspensionRequest, ValidateOrRejectSpaceSuspensionRequest $request)
    {
        return response($this->suspensionRepository->validateOrReject($spaceSuspensionRequest, $request->validated()));
    }
}
