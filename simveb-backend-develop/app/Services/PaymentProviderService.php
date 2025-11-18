<?php

namespace App\Services;
use App\Enums\PaymentProviderEnum;
use App\Enums\Status;
use App\Models\Config\PaymentProvider;
use Illuminate\Support\Facades\Log;

class PaymentProviderService
{
    public function checkPaymentReference($paymentProviderId, $paymentReference, $amount): array
    {
        try {
            $result = [];
            $payementProvider = $paymentProviderId ? PaymentProvider::find($paymentProviderId) : PaymentProvider::where('is_default', true)->first();

            switch ($payementProvider->code) {
                case PaymentProviderEnum::fedapay->name:
                    $fedapayTransaction = (new FedaPayTransactionService)->retrieve($paymentReference);
                    if ( $fedapayTransaction->status != Status::approved->name || $fedapayTransaction->amount != $amount){
                        $result['value'] = false;
                        $result['message'] = "Oups! Désolé le paiement de votre commande n'a pas abouti";
                    } else {
                        $result['value'] = true;
                        $result['message'] = "";
                    }
                    break;
                    case PaymentProviderEnum::kkiapay->name:
                        $kkiapayTransaction = (new KkiaPayTransactionService)->retrieve($paymentReference);
                        if ( strtolower($kkiapayTransaction->status) != Status::success->name || $kkiapayTransaction->amount != $amount){
                            $result['value'] = false;
                            $result['message'] = "Oups! Désolé le paiement de votre commande n'a pas abouti";
                        } else {
                            $result['value'] = true;
                            $result['message'] = "";
                        }
                        break;

                default:
                    break;
            }


            return $result;
        } catch (\Exception $exception){
            Log::debug($exception);
            $result['value'] = false;
            $result['message'] = "Oups! un problème est survenu!";
            return $result;
        }
    }
}
