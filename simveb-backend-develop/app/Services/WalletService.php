<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\TransactionTypesEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    public function __construct(private readonly FedapayService $fedapayService)
    {
    }

    public function getTransactions()
    {
        $wallet = getOnlineProfile()->affiliate->wallet;
        if ($wallet) {
            return $wallet->transactions()->orderBy('created_at', 'desc')->get();
        }
        return [];
    }

    public function recharge(array $data)
    {
        DB::beginTransaction();
        try {
            $authorProfile = getOnlineProfile();

            $wallet = $authorProfile->space->wallet;

            if (!$wallet) {
                $wallet = $authorProfile->space->wallet()->create();
            }

            $transaction = $wallet->transactions()->create([
                'payment_reference' => $data['payment_reference'],
                'amount' => $data['amount'],
                'total_amount' => $data['amount'],
                'type' => TransactionTypesEnum::credit->name,
                'status' => Status::approved->name,
            ]);

            $wallet->update(['balance' => $wallet->balance + $data['amount']]);

            DB::commit();

            return $transaction;
        } catch (\Exception $exeption) {
            DB::rollBack();
            Log::debug($exeption);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
