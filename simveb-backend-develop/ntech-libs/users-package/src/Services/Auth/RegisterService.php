<?php

namespace Ntech\UserPackage\Services\Auth;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RegisterService
{
    private $registerPersonService;

    private $registerCompanyService;

    public function __construct()
    {
        $this->registerPersonService = new RegisterPersonService;
        $this->registerCompanyService = new RegisterCompanyService;
    }

    public function initRegistration(array $data)
    {
        if ($data['person_type'] == 'physical') {
            return $this->registerPersonService->initRegistration($data);
        } else {
            return $this->registerCompanyService->initRegistration($data);
        }
    }

    public function resendOtp(array $data)
    {
        if ($data['person_type'] == 'physical') {
            return $this->registerPersonService->resendOtp($data);
        } else {
            return $this->registerCompanyService->resendOtp($data);
        }
    }

    public function checkOtp(array $data)
    {
        $otpKey = request()->ip() . '-one-time-password';
        $otpCache = Cache::get($otpKey);

        if (!$otpCache) {
            return [false, ['message' => 'Code OTP expiré. Veuillez le renouveler.', 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
        }

        if ($data['person_type'] == 'physical') {
            return $this->registerPersonService->checkOtp($data, $otpKey, $otpCache);
        } else {
            return $this->registerCompanyService->checkOtp($data, $otpKey, $otpCache);
        }
    }

    public function store(array $data)
    {
        if ($data['person_type'] == 'physical') {
            return $this->registerPersonService->store($data);
        } else {
            return $this->registerCompanyService->store($data);
        }
    }
}
