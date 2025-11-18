<?php

namespace App\Http\Controllers\Client\Demand;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Repositories\Demand\OrderRepository;

class OrderController extends Controller
{
    public function __construct(private readonly OrderRepository $repository)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getClientOrders());
    }

    public function show(Order $order)
    {
        return response($this->repository->getClientOrder($order));
    }
}
