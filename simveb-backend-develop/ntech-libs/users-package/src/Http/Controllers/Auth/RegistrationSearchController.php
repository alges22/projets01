<?php

namespace Ntech\UserPackage\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Ntech\UserPackage\Services\Auth\RegistrationSearchService;

class RegistrationSearchController extends Controller
{
    public function __construct(private readonly RegistrationSearchService $service)
    {
    }

    public function states()
    {
        return $this->successResponse($this->service->states());
    }

    public function towns()
    {
        return $this->successResponse($this->service->towns());
    }

    public function districts()
    {
        return $this->successResponse($this->service->districts());
    }

    public function villages()
    {
        return $this->successResponse($this->service->villages());
    }

}
