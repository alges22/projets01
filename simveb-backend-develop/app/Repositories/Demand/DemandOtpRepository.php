<?php

namespace App\Repositories\Demand;

use App\Consts\NotificationNames;
use App\Models\DemandOtp;
use App\Services\IdentityService;
use App\Services\VehicleOwnerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DemandOtpRepository
{

    private VehicleOwnerService $vehicleOwnerService;

    public function __construct()
    {
        $this->vehicleOwnerService = new VehicleOwnerService;
    }

    /**
     * @throws \Exception
     */
    public function store(array $data): array
    {

        DB::beginTransaction();
        try {
            $otp = in_array(app()->env, ['local', 'dev', 'development', 'staging']) ? '1234' : strtoupper(Str::random(4));
            $demandOtp = [
                'owner_otp' => Hash::make($otp),
                'owner_npi' => $data['owner_npi'] ?? null,
                'owner_ifu' => $data['owner_ifu'] ?? null,
            ];
            $owner = isset($data["owner_npi"]) ? (new IdentityService)->getIdentityByNpi($data["owner_npi"]) : (new IdentityService)->getIdentityByIfu($data["owner_ifu"], true);
            $notifData = [
                'purpose' =>  "Pour poursuivre l'enregistrement de votre entreprise",
                'otp' => $otp,
                'time' => 2
            ];
            $email = $owner->email;
            //Notification::route('sms', $owner->telephone)->notify(new NotificationSender(name: NotificationNames::DEMAND_OTP_VERIFICATION, data: $notifData));
            sendMail(
                $email,
                null,
                NotificationNames::DEMAND_OTP_VERIFICATION,
                data: $notifData
            );

            if (isset($data["buyer_npi"]) || isset($data["buyer_ifu"])) {
                $buyerOtp = in_array(app()->env, ['local', 'dev', 'development', 'staging']) ? '1234' : strtoupper(Str::random(4));
                $buyer = isset($data["buyer_npi"]) ? (new IdentityService)->getIdentityByNpi($data["buyer_npi"]) : (new IdentityService)->getIdentityByIfu($data["buyer_ifu"], true);
                $demandOtp[] = [
                    'buyer_otp' => Hash::make($buyerOtp),
                    'buyer_npi' => $data["buyer_npi"],
                ];
                $notifData = [
                    'purpose' => "Pour poursuivre l'enregistrement de votre entreprise",
                    'otp' => $buyerOtp,
                    'time' => 5
                ];
                //Notification::route('sms', $buyer->telephone)->notify(new NotificationSender(name: NotificationNames::DEMAND_OTP_VERIFICATION, data: $notifData));
                sendMail(
                    $buyer->email,
                    null,
                    NotificationNames::DEMAND_OTP_VERIFICATION,
                    data: $notifData
                );
            }

            $demandOtp['expire_at'] = now()->addMinutes(2);
            $demandOtp = DemandOtp::query()->create($demandOtp);

            DB::commit();
            return [
                'message' => "Un code de validation a été envoyé sur ce numéro/email " . Str::mask($email, '*', 4, 4),
                'authorization_id' => $demandOtp->id,
                'expire_at' => $demandOtp->expire_at,
                'expire_in' => now()->diffInSeconds($demandOtp->expire_at)
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($data): array
    {
        $demandOtp = DemandOtp::query()->find($data['authorization_id']);
        $isValid = false;
        $message = "Code OTP invalid";

        if (Carbon::parse($demandOtp->expire_at)->isPast()) {
            $message = "OTP expiré";
        } else {
            if (!$demandOtp->is_verified && Hash::check($data['owner_otp'], $demandOtp->owner_otp)) {
                $isValid = true;
                $message = "OTP vérifié";
            }

            if (!$demandOtp->is_verified && $demandOtp->buyer_otp) {
                if (Hash::check($data['buyer_otp'], $demandOtp->buyer_otp)) {
                    $isValid = true;
                    $message = "OTP vérifié";
                } else {
                    $isValid = false;
                }
            }

            if ($isValid) {
                $demandOtp->update([
                    'is_verified' => true,
                    'verified_at' => now(),
                ]);
            }
        }

        return $isValid ? [
            "message" => $message,
            "verified" => true,
            "authorization_id" => $demandOtp->otp,
        ] :
            [
                "message" => $message,
                "verified" => false,
                "authorization_id" => $demandOtp->id,
            ];
    }
}
