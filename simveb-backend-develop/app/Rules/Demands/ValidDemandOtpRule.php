<?php

namespace App\Rules\Demands;

use App\Models\DemandOtp;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

readonly class ValidDemandOtpRule implements ValidationRule
{
    public function __construct(private ?string $authorizationId = null, private ?string $target = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if ($this->authorizationId !== null){
            $demandOtp = DemandOtp::query()->find($this->authorizationId);
            $isValid = true;
            $message = "Code OTP invalid";

            if (Carbon::parse($demandOtp->expire_at)->isPast()){
                $message = "Ce code OTP est déjà expiré";
                $isValid = false;
            }else {
                if ($demandOtp->is_verified || !Hash::check($value, $demandOtp->{$this->target})) {
                    $isValid = false;
                    $message = "Code OTP non valide";
                }
            }
            if (!$isValid){
                $fail(__($message));
            }
        }else{
            $demandOtp = DemandOtp::query()->find($value);
            if ($value && !$demandOtp?->is_verified) {
                $fail(__("Cette demande n'a pas été authorisé par le propriétaire"));
            }
        }
    }
}
