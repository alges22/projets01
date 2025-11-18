<?php

namespace App\Http\Controllers\Client\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;

class SubmitOrderController extends Controller
{

    public function store(OrderRequest $request, OrderService $service)
    {
        return $this->successResponse($service->submitOrder($request->order_id, $request->payment_reference));
    }
}
