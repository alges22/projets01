<?php

namespace App\Http\Controllers\Client\Order;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $service) {}

    public function generate(Order $order)
    {
        return response($this->service->generate($order));
    }
}
