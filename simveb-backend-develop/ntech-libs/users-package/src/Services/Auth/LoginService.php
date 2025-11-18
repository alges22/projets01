<?php

namespace Ntech\UserPackage\Services\Auth;

use App\Exceptions\UnexceptedErrorException;
use App\Services\IdentityService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Ntech\MetadataPackage\Enums\MetaDataKeys;

class LoginService
{
    private $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService;
    }

    public function sendOtp(array $data)
    {
        try {
            $retrievePerson = (new IdentityService)->showByNpi($data['npi'])->response()->getData(true)['data'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if (!isset($retrievePerson['npi'])) {
            return [false, ['message' => 'Ce NPI n\'est pas reconnu.', 'code' => ResponseAlias::HTTP_NOT_FOUND]];
        } else {
            $userData = $retrievePerson;

            updateOrRememberCache($data['npi'] . '-user-data', (60 * 30), [
                'npi' => $userData['npi'],
                'telephone' => $userData['telephone'],
                'email' => $userData['email'],
            ]);

            $this->otpService->processOtp(
                canal: 'sms',
                npi: $userData['npi'],
                telephone: $userData['telephone'],
                email: $userData['email'],
                smsPurpose: 'Votre mot de passe de connexion à SIMVEB est : ',
                emailPurpose: 'Pour vous connecter à SIMVEB'
            );

            return [
                true,
                [
                    'npi' => $data['npi'],
                    'telephone' => maskTelephone($userData['telephone']),
                    'otp_duration' => getMetaValue(MetaDataKeys::otp_duration->name),
                    'message' => __('auth.otp_sent_on_phone_number'),
                ]
            ];
        }
    }

    public function resendOtp(array $data)
    {
        $userData = Cache::get($data['npi'] . '-user-data');

        if (!$userData) {
            return [false, ['message' => __('auth.information_cache_timeout'), 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
        }

        $this->otpService->processOtp(
            canal: 'sms',
            npi: $userData['npi'],
            telephone: $userData['telephone'],
            email: $userData['email'],
            smsPurpose: 'Votre mot de passe de connexion à SIMVEB est : ',
            emailPurpose: 'Pour vous connecter à SIMVEB'
        );

        return [
            true,
            [
                'npi' => $data['npi'],
                'telephone' => maskTelephone($userData['telephone']),
                'otp_duration' => getMetaValue(MetaDataKeys::otp_duration->name),
                'message' => __('auth.otp_sent_on_phone_number'),
            ]
        ];
    }
}
