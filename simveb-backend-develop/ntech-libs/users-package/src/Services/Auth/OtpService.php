<?php

namespace Ntech\UserPackage\Services\Auth;

use App\Consts\NotificationNames;
use App\Consts\Utils;
use Illuminate\Support\Facades\Hash;
use App\Services\SmsService;
use Ntech\MetadataPackage\Enums\MetaDataKeys;

class OtpService
{
    public function processOtp(string $canal = 'sms', string $telephone = '', string $email = '', string $npi = '', string $ifu = '', string $emailPurpose = '', string $smsPurpose = '')
    {
        $otp = in_array(app()->env, ['local', 'dev', 'development', 'staging']) || in_array($npi, Utils::LOCAL_NPI) ? '1234' : str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $otpData['otp'] = Hash::make($otp);

        if ($canal == 'sms' && $npi) {
            $otpData['npi'] = $npi;
        } elseif ($canal == 'email' && $ifu) {
            $otpData['ifu'] = $ifu;
        }

        $key = request()->ip() . '-one-time-password';

        updateOrRememberCache($key, (60 * getMetaValue(MetaDataKeys::otp_duration->name)), $otpData);

        $notifData = [
            'purpose' => $emailPurpose ?? 'Pour poursuivre l\'enregistrement de votre entreprise',
            'otp' => $otp,
            'time' => 5
        ];

        if ($canal == 'sms' && !in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
            $message = $smsPurpose ?? 'Pour poursuivre l\'enregistrement de votre entreprise ';
            $message .= $otp;

            (new SmsService)->send($telephone, $message);
        }

        if (!in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
            sendMail(
                $email,
                null,
                NotificationNames::OTP_VERIFICATION,
                $notifData
            );
        }
    }
}
