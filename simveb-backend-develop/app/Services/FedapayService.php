<?php

namespace App\Services;

use FedaPay\FedaPay;
use FedaPay\Transaction;

class FedapayService
{
    private function setConfig()
    {
        FedaPay::setApiKey(config('fedapay.secret_key'));
        FedaPay::setEnvironment(config('fedapay.environment'));
    }

    private function createTransaction($data)
	{
        $this->setConfig();
		return Transaction::create($data);
	}

    public function retrieveTransaction($transactionId)
	{
        $this->setConfig();
		return Transaction::retrieve($transactionId);
	}

    public function newTransaction(string $description, $amount, string $telephone, string $email, ?string $firstname = '', ?string $lastname = '', array $metadata = [])
    {
        return [
            'description' => $description,
            'amount' => $amount,
            'currency' => ['iso' => 'XOF'],
            'callback_url' => route('fedapay-webhook.event'),
            'customer' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email ?? uniqid() . '@gmail.com',
                'phone_number' => [
                    'number' => "+229{$telephone}",
                    'country' => 'bj'
                ],
            ],
            'custom_metadata' => $metadata
        ];
    }

    public function processPaymentWithoutRedirection($data, $telephone)
	{
		$transaction = $this->createTransaction($data);

		$token = $transaction->generateToken()->token;
		$response = $transaction->sendNowWithToken(getPhoneNumberGsm($telephone), $token);

		$fedapayTransactionId = data_get($response, 'payment_intent.intentable_id');
		$transaction = $this->retrieveTransaction($fedapayTransactionId);
		data_set($transaction, 'payment_intent_id', data_get($response, 'payment_intent.id'));

		return $transaction;
	}
}
