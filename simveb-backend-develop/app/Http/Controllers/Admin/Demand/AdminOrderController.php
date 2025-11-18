<?php

namespace App\Http\Controllers\Admin\Demand;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Repositories\Demand\OrderRepository;

class AdminOrderController extends Controller
{
    public function __construct(private readonly OrderRepository $repository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return
            $this->repository->getOrders()->paginate(request('per_page', 15));
    }

    public function show(Order $order)
    {
        return response($this->repository->getOrder($order));
    }

    /**
     *
     */
    public function stats()
    {
        return response($this->repository->getOrderStats());
    }

    /**
     * Display a listing of the resource.
     */
    public function excelExport()
    {
        return $this->repository->excelExport();
    }

    /**
     * Display a listing of the resource.
     */
    public function pdfExport()
    {
        return $this->repository->pdfExport();
    }
}
