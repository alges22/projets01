<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Order\Transaction;

class FedapayWebhookService
{
    public function processTransactionApproved($entity)
    {
        if ($transactionId = data_get($entity, 'custom_metadata.transaction_id')) {
            $transaction = Transaction::find($transactionId);

            if ($transaction && $transaction->status == Status::pending->name) {
                $transaction->update(['status' => Status::validated->name]);

                $transaction->model->update([
                    'balance' => $transaction->model->balance + $transaction->amount,
                ]);
            }
        }
    }
}
