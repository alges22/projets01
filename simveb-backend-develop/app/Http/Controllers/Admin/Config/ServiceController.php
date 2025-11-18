<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Config\Service;
use App\Repositories\ServiceRepository;

class ServiceController extends Controller
{

    public function __construct(private ServiceRepository $repository)
    {
        // $this->authorizeResource(Service::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(request('paginate', false), ['serviceVehicleCategories','organization']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        return response($this->repository->store($request->validated(),$request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return response($this->repository->show($service));
    }

    public function create()
    {
        return response($this->repository->create());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return response([
            'service' => $service
        ] + $this->repository->create());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        return response($this->repository->update($service,$request->validated(),$request)->load($service::relations()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        return response($this->repository->destroy($service));
    }
}
