<?php

namespace App\Http\Controllers\Space;

use App\Http\Controllers\Controller;
use App\Http\Requests\Space\SpaceRegistrationRejectionFormRequest;
use App\Http\Requests\Space\SpaceRegistrationRequestFormRequest;
use App\Models\Space\SpaceRegistrationRequest;
use App\Repositories\Space\SpaceRegistrationRequestRepository;
use App\Traits\CrudRepositoryTrait;

class SpaceRegistrationRequestController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly SpaceRegistrationRequestRepository $spaceRegistrationRequestRepository)
    {
        $this->initRepository(SpaceRegistrationRequest::class);
        $this->authorizeResource(SpaceRegistrationRequest::class);
        $this->middleware('permission:validate-space-registration-request')->only(['validateRegistration']);
        $this->middleware('permission:reject-space-registration-request')->only(['rejectRegistration']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function create()
    {
        return response($this->spaceRegistrationRequestRepository->getCreateData());
    }

    /**
     * @param SpaceRegistrationRequestFormRequest $request
     * @return Response|ResponseFactory
     */
    public function store(SpaceRegistrationRequestFormRequest $request)
    {
        return $this->formatedResponse($this->spaceRegistrationRequestRepository->store($request->validated()));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true, [
            'profileType:id,name',
            'institution:id,name',
            'creator.identity:id,firstname,lastname,telephone,email,npi',
        ]));
    }

    public function show(SpaceRegistrationRequest $spaceRegistrationRequest)
    {
        return response($spaceRegistrationRequest->load(SpaceRegistrationRequest::relations()));
    }

    public function validateRegistration(SpaceRegistrationRequest $spaceRegistrationRequest)
    {
        return $this->formatedResponse($this->spaceRegistrationRequestRepository->validateRegistration($spaceRegistrationRequest));
    }

    public function rejectRegistration(SpaceRegistrationRejectionFormRequest $request, SpaceRegistrationRequest $spaceRegistrationRequest)
    {
        return $this->formatedResponse($this->spaceRegistrationRequestRepository->rejectRegistration($spaceRegistrationRequest, $request->validated()));
    }
}
