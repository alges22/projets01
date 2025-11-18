<?php
namespace App\Traits;

use App\Models\Order\Transaction;

trait HasTransactions
{

    public function transactions()
    {
        return $this->morphMany(Transaction::class,"model","model_type");
    }
    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'model');
    }

}
