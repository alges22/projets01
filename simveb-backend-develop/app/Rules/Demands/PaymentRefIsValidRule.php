<?php

namespace App\Rules\Demands;

use App\Services\PaymentProviderService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class PaymentRefIsValidRule implements ValidationRule
{

    public function __construct(private $paymentProviderId, private $amount)
    {
    }


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $result = (new PaymentProviderService)->checkPaymentReference($this->paymentProviderId, $value, $this->amount);
            if (!$result['value']){
                $fail("Oups! Désolé le paiement de votre commande n'a pas abouti");
            }
        }catch (\Exception $exception){
            $fail("Oups! Désolé le paiement de votre commande n'a pas abouti");
            Log::debug($exception);
        }

    }
}
