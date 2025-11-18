<?php

namespace App\Http\Controllers\Space;

use App\Http\Controllers\Controller;
use App\Http\Requests\Space\SpaceSuspensionRequesLiftingtFormRequest;
use App\Http\Requests\Space\ValidateOrRejectSpaceSuspensionLiftingRequest;
use App\Models\Space\SpaceSuspensionLiftingRequest;
use App\Repositories\Space\SpaceSuspensionLiftingRequestRepository;
use App\Traits\CrudRepositoryTrait;

class SpaceSuspensionLiftingRequestController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly SpaceSuspensionLiftingRequestRepository $suspensionRepository)
    {
        $this->initRepository(SpaceSuspensionLiftingRequest::class);
        $this->authorizeResource(SpaceSuspensionLiftingRequest::class);
        $this->middleware('permission:validate-space-suspension-lifting-request')->only(['validateOrReject']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true, SpaceSuspensionLiftingRequest::relations()));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function show(SpaceSuspensionLiftingRequest $spaceSuspensionLiftingRequest)
    {
        return response($spaceSuspensionLiftingRequest->load($spaceSuspensionLiftingRequest::relations()));
    }

    public function create()
    {
        return response($this->suspensionRepository->createData());
    }

    public function store(SpaceSuspensionRequesLiftingtFormRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = getOnlineProfile()->id;

        return response($this->repository->store($data));
    }

    public function edit(SpaceSuspensionLiftingRequest $spaceSuspensionLiftingRequest)
    {
        return response(array_merge(['space_suspension_request' => $spaceSuspensionLiftingRequest], $this->suspensionRepository->createData()));
    }

    public function update(SpaceSuspensionRequesLiftingtFormRequest $request, SpaceSuspensionLiftingRequest $spaceSuspensionLiftingRequest)
    {
        return response($this->repository->update($spaceSuspensionLiftingRequest, $request->validated()));
    }

    public function validateOrReject(SpaceSuspensionLiftingRequest $spaceSuspensionLiftingRequest, ValidateOrRejectSpaceSuspensionLiftingRequest $request)
    {
        return response($this->suspensionRepository->validateOrReject($spaceSuspensionLiftingRequest, $request->validated()));
    }
}
