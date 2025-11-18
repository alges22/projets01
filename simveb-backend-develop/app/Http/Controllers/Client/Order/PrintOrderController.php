<?php

namespace App\Http\Controllers\Client\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintOrders\ConfirmPrintOrderAffectationRequest;
use App\Http\Requests\PrintOrders\PrintGrayCardRequest;
use App\Http\Requests\PrintOrders\PrintOrderRequest;
use App\Http\Requests\PrintOrders\PrintPlateRequest;
use App\Http\Requests\PrintOrders\PrintValidationRequest;
use App\Models\Treatment\PrintOrder;
use App\Repositories\PrintOrderRepository;
use App\Services\PrintOrderService;

class PrintOrderController extends Controller
{
    public function __construct(private readonly PrintOrderService $service, private readonly PrintOrderRepository $repository)
    {
        $this->middleware('permission:emit-print-order')->only('store');
        $this->middleware('permission:show-print-order')->only('search', 'show');
        $this->middleware('permission:confirm-print-order-affectation')->only('confirmAffectation');
        $this->middleware('permission:print-plate')->only('printPlate');
        $this->middleware('permission:print-gray-card')->only('printGrayCard');
        $this->middleware('permission:validate-print')->only('validateOrRejectPrint');
    }

    public function search()
    {
        [$statusCode, $result] = $this->repository->search();

        return response($result, $statusCode);
    }

    public function index()
    {
        return response($this->repository->getAll(true));
    }

    public function store(PrintOrderRequest $request)
    {
        $result = $this->repository->store($request->validated());

        return $result['success'] ? $this->createdResponse($result['data']) : $this->errorResponse($result['message'], $result['code']);
    }

    public function show(PrintOrder $printOrder)
    {
        $printOrder->load(PrintOrder::relations());
        $printOrder->immatriculation = $printOrder->immatriculation;

        return response($printOrder);
    }

    public function confirmAffectation(ConfirmPrintOrderAffectationRequest $request)
    {
        return response($this->service->confirmAffectation($request->validated()));
    }

    public function printPlate(PrintPlateRequest $request)
    {
        return response($this->service->printPlate($request->validated()));
    }

    public function printGrayCard(PrintGrayCardRequest $request)
    {
        return response($this->service->printGrayCard($request->validated()));
    }

    public function validateOrRejectPrint(PrintValidationRequest $request)
    {
        return response($this->service->validateOrRejectPrint($request->validated()));
    }
}
