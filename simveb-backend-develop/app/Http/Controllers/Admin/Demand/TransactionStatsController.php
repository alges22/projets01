<?php

namespace App\Http\Controllers\Admin\Demand;

use App\Http\Controllers\Controller;
use App\Repositories\Demand\TransactionRepository;
use Illuminate\Http\Request;

class TransactionStatsController extends Controller
{
    public function __construct(private readonly TransactionRepository $repository)
    {
        //
    }

    /**
     *
     */
    public function total()
    {
        return response($this->repository->getTotalAmount());
    }
}
