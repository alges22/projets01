<?php

namespace Ntech\UserPackage\Services\Auth;

use App\Consts\NotificationNames;
use App\Enums\ProfileTypesEnum;
use App\Exceptions\UnexceptedErrorException;
use App\Services\IdentityService;
use Illuminate\Support\Facades\Log;
use App\Models\Auth\ProfileType;
use App\Models\Institution\Institution;
use App\Repositories\Space\SpaceRegistrationRequestRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ntech\MetadataPackage\Enums\MetaDataKeys;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RegisterCompanyService
{
    private $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService;
    }

    public function initRegistration(array $data)
    {
        if (Institution::where('ifu', $data['ifu'])->first()) {
            return [false, ['message' => 'Cette entreprise a déjà été enregistrée.', 'code' => ResponseAlias::HTTP_CONFLICT]];
        }

        try {
            $retrieveCompany = (new IdentityService)->getIdentityByIfu($data['ifu']);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if (!isset($retrieveCompany['email'])) {
            return [false, ['message' => 'Cet IFU n\'est pas reconnue.', 'code' => ResponseAlias::HTTP_NOT_FOUND]];
        } else {
            $companyData = $retrieveCompany;

            updateOrRememberCache($data['ifu'] . '-company-data', (60 * 30), $companyData);

            $this->otpService->processOtp(canal: 'email', email: $companyData['email'], ifu: $data['ifu']);

            return [true, ['ifu' => $data['ifu'], 'email' => hideEmailAddress($companyData['email']), 'otp_duration' => getMetaValue(MetaDataKeys::otp_duration->name), 'message' => "IFU valide. Un code à usage unique a été envoyé sur l'email de votre entreprise."]];
        }
    }

    public function resendOtp(array $data)
    {
        $companyData = Cache::get($data['ifu'] . '-company-data');

        if (!$companyData) {
            return [false, ['message' => __('auth.information_cache_timeout'), 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
        }

        $this->otpService->processOtp(canal: 'email', email: $companyData['email'], ifu: $data['ifu']);

        return [true, ['ifu' => $data['ifu'], 'email' => hideEmailAddress($companyData['email']), 'otp_duration' => getMetaValue(MetaDataKeys::otp_duration->name), 'message' => "Un code à usage unique a été envoyé sur l'email de votre entreprise."]];
    }

    public function checkOtp(array $data, string $otpKey, mixed $otpCache)
    {
        if (Hash::check($data['otp'], $otpCache['otp']) && $otpCache['ifu'] == $data['ifu']) {
            Cache::forget($otpKey);

            $companyData = Cache::get($data['ifu'] . '-company-data');

            if (!$companyData) {
                return [false, ['message' => __('auth.information_cache_timeout'), 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
            }

            return [true, ['message' => 'Code OTP valide.', 'company_data' => $companyData]];
        } else {
            return [false, ['message' => 'Code OTP invalide.', 'code' => ResponseAlias::HTTP_NOT_FOUND]];
        }
    }

    public function store(array $data)
    {
        $companyData = Cache::get($data['ifu'] . '-company-data');

        if (!$companyData) {
            return [false, ['message' => __('auth.information_cache_timeout'), 'code' => ResponseAlias::HTTP_REQUEST_TIMEOUT]];
        }

        try {
            $checkNpi = (new IdentityService)->showByNpi($data['first_member_npi'])->response()->getData(true)['data'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if (!isset($checkNpi['npi'])) {
            Log::debug('error', [$checkNpi]);

            return [false, ['message' => 'Le numéro NPI du membre est invalide.', 'code' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY]];
        }

        DB::beginTransaction();
        try {
            $company = (new IdentityService)->storeByIfu($companyData);

            $data = [...$data, ...$companyData];
            $data['profile_type_id'] = ProfileType::where('code', ProfileTypesEnum::company->name)->first()->id;
            $data['institution_id'] = $company->id;

            $registrationRequest = (new SpaceRegistrationRequestRepository)->store($data);

            if (!in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
                sendNotification(
                    NotificationNames::COMPANY_REGISTRATION_SUBMITTED,
                    $company,
                    ['company_name' => $company->name],
                    ['mail', 'sms']
                );
            }

            DB::commit();

            Cache::forget($data['ifu'] . '-company-data');

            return [true, ['message' => 'Votre demande d\'enregistrement a été soumise avec succès.']];
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
