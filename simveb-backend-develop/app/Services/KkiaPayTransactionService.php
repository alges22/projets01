<?php

namespace App\Services;

use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Exceptions\NotificationConfigNotFoundException;
use App\Models\GrayCardDuplicate;
use App\Models\Order\Demand;
use App\Models\Order\Transaction;
use App\Models\PlateDuplicate;
use App\Notifications\NotificationSender;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class KkiaPayTransactionService
{

    /**
     * @throws NotificationConfigNotFoundException
     */
    public function createTransaction($data)
    {
        $kkiaPayTransaction =  $this->transaction()->verifyTransaction($data['payment_id']);

        if ($data['service_type'] == 'immatriculation-duplicate') {
            $modelType = PlateDuplicate::class;
        } elseif ($data['service_type'] == 'immatriculation') {
            $modelType = Demand::class;
        } elseif ($data['service_type'] == 'gray-card-duplicate') {
            $modelType = GrayCardDuplicate::class;
        }

        $data['status']  = $kkiaPayTransaction->status;
        $data['mode'] = $kkiaPayTransaction->mode;
        $data['amount'] = $kkiaPayTransaction->amount;
        $data['fees'] = $kkiaPayTransaction->fees;
        $data['total_amount'] = $data['amount'] + $data['fees'];
        // TODO: add app reference on this column
        $data['reference'] = $kkiaPayTransaction->reference;
        $data['payment_reference'] = $kkiaPayTransaction->reference;
        $data['payment_id'] = $kkiaPayTransaction->id;
        $data['model_id'] = $data['demand_id'];
        $data['model_type'] = $modelType;

        $transaction = Transaction::query()->create($data);
        $demand = $modelType::findOrFail($data['demand_id']);

        $demand->update(['payment_status' => $kkiaPayTransaction->status]);

        if ($data['status'] == Status::approved->name)
        {
            sendMail(
                null,
                $demand->vehicleOwner->identity,
                NotificationNames::DEMAND_PAID,                   
                ['reference' => $demand->reference]
            );
        }

        return $transaction;
    }

    public function retrieve($transactionId)
    {
        return $this->transaction()->verifyTransaction($transactionId);
    }
    public function transaction() 
    {
        return new \Kkiapay\Kkiapay(config('app.kkiapay_pk'),config('app.kkiapay_sk'),config('app.kkiapay_sec'),$sandbox = config('app.kkiapay_sand'));
    }

}
