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
use FedaPay\FedaPay;
use FedaPay\Transaction as FedaPayTransaction;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class FedaPayTransactionService
{

    /**
     * @throws NotificationConfigNotFoundException
     */
    public function createTransaction($data)
    {
        $fedaPayTransaction =  FedaPayTransaction::retrieve($data['payment_id']);

        if ($data['service_type'] == 'immatriculation-duplicate') {
            $modelType = PlateDuplicate::class;
        } elseif ($data['service_type'] == 'immatriculation') {
            $modelType = Demand::class;
        } elseif ($data['service_type'] == 'gray-card-duplicate') {
            $modelType = GrayCardDuplicate::class;
        }

        $data['status']  = $fedaPayTransaction->status;
        $data['mode'] = $fedaPayTransaction->mode;
        $data['amount'] = $fedaPayTransaction->amount;
        $data['fees'] = $fedaPayTransaction->fees;
        $data['total_amount'] = $data['amount'] + $data['fees'];
        // TODO: add app reference on this column
        $data['reference'] = $fedaPayTransaction->reference;
        $data['payment_reference'] = $fedaPayTransaction->reference;
        $data['payment_id'] = $fedaPayTransaction->id;
        $data['model_id'] = $data['demand_id'];
        $data['model_type'] = $modelType;

        $transaction = Transaction::query()->create($data);
        $demand = $modelType::findOrFail($data['demand_id']);

        $demand->update(['payment_status' => $fedaPayTransaction->status]);

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
        return FedaPayTransaction::retrieve($transactionId);
    }

}
