<?php
namespace App\Traits;

use App\Models\Wallet;

trait HasWallet
{
    public function wallet()
    {
        return $this->morphOne(Wallet::class, 'model', 'model_type');
    }
}
