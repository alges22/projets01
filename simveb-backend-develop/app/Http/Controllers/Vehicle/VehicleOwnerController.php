<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SubscribeOwnerRequest;
use App\Http\Requests\Vehicle\VehicleOwnerRequest;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\Vehicle\VehicleOwnerRepository;
use App\Services\IdentityService;
use Illuminate\Http\Request;

class VehicleOwnerController extends Controller
{

    public function __construct(private readonly VehicleOwnerRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->repository->create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleOwnerRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getOwnerInfo(Request $request, VehicleOwnerRepository $ownerRepository)
    {
        return response($ownerRepository->getOwnerByEmail($request->email));
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleOwner $vehicleOwner)
    {
        return response($vehicleOwner->load($vehicleOwner::relations()));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleOwner $vehicleOwner)
    {
        return response([
            ['vehicleOwner' => $vehicleOwner],
            ...$this->repository->create()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleOwnerRequest $request, VehicleOwner $vehicleOwner)
    {
            return response($this->repository->update($vehicleOwner,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleOwner $vehicleOwner)
    {
        return response($this->repository->destroy($vehicleOwner));
    }
}
