<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\ReservedPlateNumber\ReservedPlateNumberValidationRequest;
use App\Http\Requests\ReservedPlateNumberRequest;
use App\Models\Config\ReservedPlateNumber;
use App\Services\ReservedPlateNumberService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Foundation\Application;

class ReservedPlateNumberController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(ReservedPlateNumber::class);
        $this->authorizeResource(ReservedPlateNumber::class);
        $this->service = new ReservedPlateNumberService();
        $this->middleware('permission:validate-reserved-plate-number')->only('validateOrInvalidate');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Application|\Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @param ReservedPlateNumberRequest $request
     * @return Application|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(ReservedPlateNumberRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * @param ReservedPlateNumber $reservedPlateNumber
     * @return Application|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(ReservedPlateNumber $reservedPlateNumber)
    {
        return response($reservedPlateNumber->load(ReservedPlateNumber::relations()));
    }

    /**
     * @param ReservedPlateNumberRequest $request
     * @param ReservedPlateNumber $reservedPlateNumber
     * @return Response|ResponseFactory
     */
    public function update(ReservedPlateNumberRequest $request, ReservedPlateNumber $reservedPlateNumber)
    {
        return response($this->repository->update($reservedPlateNumber, $request->validated()));
    }

    /**
     * @param ReservedPlateNumber $reservedPlateNumber
     * @return Response|ResponseFactory
     */
    public function destroy(ReservedPlateNumber $reservedPlateNumber)
    {
        return response($this->repository->destroy($reservedPlateNumber));
    }

    public function validateOrInvalidate(ReservedPlateNumberValidationRequest $request){
        return response($this->service->validateOrReject($request->validated()));
    }
}
