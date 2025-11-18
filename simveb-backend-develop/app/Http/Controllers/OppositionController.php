<?php

namespace App\Http\Controllers;

use App\Http\Requests\OppositionRequest;
use App\Models\Opposition;
use App\Services\VehicleOwnerService;
use App\Services\OppositionService;
use App\Repositories\OppositionRepository;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OppositionController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly OppositionRepository $oppositionRepository, private readonly VehicleOwnerService $ownerService,
                                private readonly OppositionService $oppositionService)
    {
        $this->initRepository(Opposition::class);
        $this->authorizeResource(Opposition::class);

        $this->middleware('permission:store-opposition')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->oppositionRepository->getAll(true, Opposition::relations()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Opposition::class);
        return response($this->oppositionRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     * @param OppositionRequest $request
     * @return mixed
     */
    public function store(OppositionRequest $request)
    {
        return response($this->oppositionRepository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     * @param Opposition $opposition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function show(Opposition $opposition)
    {
        return response($opposition->load(Opposition::relations()));
    }


    /**
     * @param OppositionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function showVehiclesByNpiOrIfu(OppositionRequest $request)
    {

        return response($this->oppositionRepository->showVehicles($request->validated(), $request));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Opposition $opposition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Opposition $opposition
     */
    public function update(OppositionRequest $request, Opposition $opposition)
    {
        return response($this->oppositionRepository->updateOpposition($opposition, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     * @param Opposition $opposition
     */
    public function destroy(Opposition $opposition)
    {
        return response($this->oppositionRepository->delete($opposition));
    }


    public function lift(Opposition $opposition, OppositionRequest $request)
    {
        $this->authorize('lift', $opposition);
        return response($this->oppositionRepository->lift($opposition, $request->validated()));
    }


    /**
     * @param Opposition $opposition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function validateOpposition(Opposition $opposition)
    {
        $this->authorize('validate', $opposition);
        return response($this->oppositionRepository->validateOpposition($opposition));
    }


    public function reject(Opposition $opposition, OppositionRequest $request)
    {
        $this->authorize('reject', $opposition);
        return response($this->oppositionRepository->reject($opposition, $request->validated()));
    }


    public function oppositionTotal()
    {
        return response($this->oppositionRepository->oppositionTotal());
    }


    public function activeCloseOpposition()
    {
        return response($this->oppositionRepository->activeCloseOpposition());
    }
}
