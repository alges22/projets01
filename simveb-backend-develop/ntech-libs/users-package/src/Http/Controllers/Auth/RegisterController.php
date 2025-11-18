<?php

namespace Ntech\UserPackage\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Space\SpaceRegistrationRequest;
use Ntech\UserPackage\Http\Requests\Register\CheckOtpRequest;
use Ntech\UserPackage\Http\Requests\Register\InitRegistrationRequest;
use Ntech\UserPackage\Http\Requests\Register\ResendOtpRequest;
use Ntech\UserPackage\Http\Requests\Register\StoreRegistrationRequest;
use Ntech\UserPackage\Services\Auth\RegisterService;

class RegisterController extends Controller
{
    public function __construct(private readonly RegisterService $service)
    {
    }

    public function initRegistration(InitRegistrationRequest $request)
    {
        return $this->formatedResponse($this->service->initRegistration($request->validated()));
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->formatedResponse($this->service->resendOtp($request->validated()));
    }

    public function checkOtp(CheckOtpRequest $request)
    {
        return $this->formatedResponse($this->service->checkOtp($request->validated()));
    }

    public function spaceDocuments()
    {
        return ['required_document_types' => SpaceRegistrationRequest::requiredDocumentTypes()];
    }

    public function store(StoreRegistrationRequest $request)
    {
        return $this->formatedResponse($this->service->store($request->validated()));
    }
}
