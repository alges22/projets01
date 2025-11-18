<?php

namespace App\Http\Controllers\Client\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommissionRequest;
use App\Models\Order\Commission;
use App\Traits\CrudRepositoryTrait;

class CommissionController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(Commission::class);
        $this->authorizeResource(Commission::class);
    }

    public function index()
    {
        return response($this->repository->getAll());
    }

    public function store(CommissionRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    public function create()
    {
        return response([
            'calculation_bases' => Commission::CALCULATION_BASES
        ]);
    }

    public function show(Commission $commission)
    {
        return response($commission->load($commission::relations()));
    }

    public function update(CommissionRequest $request, Commission $commission)
    {
        return response($this->repository->update($commission,$request->validated()));
    }

    public function destroy(Commission $commission)
    {
        return response($this->repository->destroy($commission));
    }
}
