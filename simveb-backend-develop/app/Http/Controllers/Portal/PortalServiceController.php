<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Config\Service;
use App\Models\Config\ServiceType;
use App\Repositories\PortalRepository;
use App\Services\TransactionService;

class PortalServiceController extends Controller
{
    public function __construct(private readonly PortalRepository $portalRepository)
    {}

    public function serviceTypes()
    {
        return response(ServiceType::all());
    }

    public function services()
    {
        return response($this->portalRepository->services());
    }

    public function service($serviceId)
    {
        return response(Service::findorFail($serviceId)->load(Service::relations()));
    }

    public function createTransaction(TransactionRequest $request, TransactionService $service)
    {
        return response($service->createTransaction($request->validated()));
    }
}
