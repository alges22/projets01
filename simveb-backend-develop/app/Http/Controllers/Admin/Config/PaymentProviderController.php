<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentProviderRequest;
use App\Models\Config\PaymentProvider;
use App\Repositories\PaymentProviderRepository;

class PaymentProviderController extends Controller
{
    public function __construct(private readonly PaymentProviderRepository $repository)
    {
        $this->authorizeResource(PaymentProvider::class);
        $this->middleware('permission:update-payment-provider')->only(['toggle', 'default']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }
    /**
     * Display a listing of the resource.
     */
    public function getActive()
    {
        return response($this->repository->getActive());
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentProvider $paymentProvider)
    {
        return response($paymentProvider->load(relations: $paymentProvider::relations()));
    }

    /**
     * @param PaymentProviderRequest $request
     * @param PaymentProvider $plateColor
     */
    public function update(PaymentProviderRequest $request, PaymentProvider $paymentProvider)
    {
        return response($this->repository->update($paymentProvider, $request->validated()));
    }

    /**
     * Toggle the specified resource.
     */
    public function toggle(PaymentProvider $paymentProvider)
    {
        return $this->successResponse($this->repository->toggle($paymentProvider));
    }

    /**
     * Define the specified resource as default.
     */
    public function default(PaymentProvider $paymentProvider)
    {
        return $this->successResponse($this->repository->default($paymentProvider));
    }
}
