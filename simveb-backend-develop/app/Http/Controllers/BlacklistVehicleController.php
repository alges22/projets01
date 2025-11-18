<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlacklistVehicle\BlacklistVehicleRequest;
use App\Http\Requests\BlacklistVehicle\ImportBlacklistVehicleRequest;
use App\Models\Config\BlacklistVehicle;
use App\Repositories\BlacklistVehicleRepository;

class BlacklistVehicleController extends Controller
{
    public function __construct(private readonly BlacklistVehicleRepository $repository)
    {
        $this->authorizeResource(BlacklistVehicle::class);
        $this->middleware('permission:store-blacklist-vehicle')->only(['fileFormat', 'import']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll()->load(BlacklistVehicle::relations()));
    }

    /**
     * @param BlacklistVehicleRequest $request
     * @return Response|ResponseFactory
     */
    public function store(BlacklistVehicleRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * @param BlacklistVehicle $blacklistVehicle
     * @return Response|ResponseFactory
     */
    public function show(BlacklistVehicle $blacklistVehicle)
    {
        return response($blacklistVehicle->load(BlacklistVehicle::relations()));
    }

    /**
     * @param BlacklistVehicle $blacklistVehicle
     * @return Response|ResponseFactory
     */
    public function destroy(BlacklistVehicle $blacklistVehicle)
    {
        return response($this->repository->destroy($blacklistVehicle));
    }

    public function fileFormat()
    {
        $path = 'format-import/blacklist_vehicles.xlsx';
        return file_exists(public_path($path)) ? response()->download(public_path($path), 'blacklist_vehicles.xlsx') : response('');
    }

    public function import(ImportBlacklistVehicleRequest $request)
    {
        return response($this->repository->import($request->validated()));
    }
}
