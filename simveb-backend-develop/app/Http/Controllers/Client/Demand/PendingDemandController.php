<?php

namespace App\Http\Controllers\Client\Demand;
use App\Http\Controllers\Controller;
use App\Repositories\Demand\DemandRepository;

class PendingDemandController extends Controller
{
    public function __construct(private readonly DemandRepository $repository)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getClientPendingDemands());
    }
}
