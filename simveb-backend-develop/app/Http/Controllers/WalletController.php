<?php

namespace App\Http\Controllers;

use App\Http\Requests\Wallet\RechargeWalletRequest;
use App\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
        $this->middleware('permission:show-wallet')->only(['show', 'transactions']);
        $this->middleware('permission:update-wallet')->only(['recharge']);
    }

    public function show()
    {
        return response(getOnlineProfile()->space?->wallet);
    }

    public function recharge(RechargeWalletRequest $request)
    {
        return response($this->walletService->recharge($request->validated()));
    }

    public function transactions()
    {
        return response($this->walletService->getTransactions());
    }
}
