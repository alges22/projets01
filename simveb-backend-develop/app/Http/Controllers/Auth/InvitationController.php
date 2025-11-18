<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationRequest;
use App\Models\Auth\Invitation;
use App\Services\InvitationService;

class InvitationController extends Controller
{
    public function __construct(private readonly InvitationService $service)
    {
        // $this->authorizeResource(Invitation::class);
    }

    public function index()
    {
        return response($this->service->getAll());
    }

    public function create()
    {
        return response($this->service->createData());
    }

    public function store(InvitationRequest $request)
    {
        return $this->formatedResponse($this->service->store($request->validated()));
    }

    public function show(Invitation $invitation)
    {
        return response($invitation->load(['space:id,institution_id,profile_type_id', 'space.institution:id,name,logo_path', 'profileType:id,code,name', 'roles:id,label']));
    }

    public function validateInvitation(Invitation $invitation)
    {
        return $this->formatedResponse($this->service->validateInvitation($invitation));
    }

    public function deny(Invitation $invitation)
    {
        return $this->formatedResponse($this->service->deny($invitation));
    }

    public function resend(Invitation $invitation)
    {
        return $this->formatedResponse($this->service->resend($invitation));
    }
}
