<?php

namespace App\Repositories\Demand;

use App\Enums\Status;
use App\Models\Order\Transaction;
use App\Repositories\Crud\AbstractCrudRepository;

class TransactionRepository extends AbstractCrudRepository
{
    public function __construct()
    {
      parent::__construct(Transaction::class);
    }

    /**
     *
     */
    public function getTotalAmount()
    {
        return [
            'total_transactions_amount' => $this->model->newQuery()->filter()->sum('amount')
        ];
    }

    /**
     *
     */
    public function getPendingAmount()
    {
        return [
            'pending_transactions_amount' => $this->model->newQuery()->where('status', Status::pending->name)->filter()->sum('amount')
        ];
    }

    /**
     *
     */
    public function getCashedAmount()
    {
        return [
            'cashed_transactions_amount' => $this->model->newQuery()->where('status', Status::approved->name)->filter()->sum('amount')
        ];
    }
}
