<?php

namespace Ntech\UserPackage\Services\Auth;

use App\Consts\NotificationNames;
use App\Enums\ProfileTypesEnum;
use App\Exceptions\UnexceptedErrorException;
use App\Models\Account\User;
use App\Models\Auth\ProfileType;
use App\Services\IdentityService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Repositories\IdentityRepository;
use Ntech\UserPackage\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Ntech\MetadataPackage\Enums\MetaDataKeys;

class RegisterPersonService
{
    private $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService;
    }

    public function initRegistration(array $data)
    {
        if (User::where('username', $data['npi'])->first()) {
            return [false, ['message' => 'Vous avez déjà un compte utilisateur.', 'code' =>  ResponseAlias::HTTP_CONFLICT]];
        }

        try {
            $retrievePerson = (new IdentityService)->showByNpi($data['npi'])->response()->getData(true)['data'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if (!isset($retrievePerson['npi'])) {
            Log::debug('error', [$retrievePerson]);

            return [false, ['message' => 'Une erreure est survenue', 'code' => ResponseAlias::HTTP_NOT_FOUND]];
        } else {
            $userData = $retrievePerson;
            $userData['email'] = $data['email'];

            updateOrRememberCache($data['npi'] . '-user-data', (60 * 30), $userData);

            $this->otpService->processOtp(canal: 'sms', telephone: $userData['telephone'], npi: $data['npi']);

            return [
                true,
                [
                    'npi' => $data['npi'],
                    'telephone' => maskTelephone($userData['telephone']),
                    'otp_duration' => getMetaValue(MetaDataKeys::otp_duration->name),
                    'message' => 'NPI valide. ' . __('auth.otp_sent_on_phone_number'),
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

        $this->otpService->processOtp(canal: 'sms', telephone: $userData['telephone'], npi: $data['npi']);

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

    public function checkOtp(array $data, string $otpKey, mixed $otpCache)
    {
        if (Hash::check($data['otp'], $otpCache['otp']) && $otpCache['npi'] == $data['npi']) {
            Cache::forget($otpKey);

            $userData = Cache::get($data['npi'] . '-user-data');

            if (!$userData) {
                return [false, ['message' => __('auth.information_cache_timeout'), 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
            }

            return [true, ['message' => 'Code OTP valide.', 'user_data' => $userData]];
        } else {
            return [false, ['message' => 'Code OTP invalide.', 'code' => ResponseAlias::HTTP_NOT_FOUND]];
        }
    }

    public function store(array $data)
    {
        $userData = Cache::get($data['npi'] . '-user-data');

        if (!$userData) {
            return [false, ['message' => __('auth.information_cache_timeout'), 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
        }

        DB::beginTransaction();
        try {
            $identity = (new IdentityRepository)->create($userData + $data);

            $user = (new UserRepository)->create([
                'identity_id' => $identity->id,
                'email' => $userData['email'],
                'username' => $data['npi'],
            ]);

            $user->profiles()->create([
                'type_id' => ProfileType::where('code', ProfileTypesEnum::user->name)->first()->id,
                'identity_id' => $identity->id,
            ]);

            if (!in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
                sendNotification(
                    NotificationNames::REGISTRATION_SUCCESSFUL,
                    $identity,
                    [
                        'link' => [
                            'text' => 'Connexion',
                            'url' => config('app.portal_url') . '/auth/login',
                        ]
                    ],
                    ['mail', 'sms']
                );
            }

            DB::commit();

            Cache::forget($data['npi'] . '-user-data');

            return [true, ['message' => 'Inscription réussie.']];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            if (!in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
                throw new UnexceptedErrorException;
            } else {
                abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
            }
        }
    }
}
