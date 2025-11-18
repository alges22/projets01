<?php

namespace App\Http\Controllers\Institution;

use App\Models\Config\Town;
use App\Models\Config\Village;
use App\Models\Config\District;
use App\Traits\CrudRepositoryTrait;
use App\Http\Controllers\Controller;
use App\Models\Institution\Institution;
use App\Models\Institution\InstitutionType;
use App\Repositories\InstitutionRepository;
use App\Http\Requests\Institution\InstitutionRequest;
use App\Models\Config\Border as ConfigBorder;

class InstitutionController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct(private readonly InstitutionRepository $institutionRepository)
    {
        $this->initRepository(Institution::class);
        $this->authorizeResource(Institution::class);
    }

    public function create()
    {
        return response([
            'types' => InstitutionType::all(),
            'towns' => Town::all(),
            'districts' => District::all(),
            'villages' => Village::all(),
            'borders' => ConfigBorder::query()->select(['id', 'name', 'town_id', 'border_country_id'])
            ->with(['town:id,code,name'])->get(),

        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, Institution::relations()));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(InstitutionRequest $request)
    {
        return response($this->institutionRepository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        return response($institution->load($institution::relations()));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(InstitutionRequest $request, Institution $institution)
    {
        return response($this->institutionRepository->update($institution,$request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        return response($this->repository->destroy($institution));
    }
}
