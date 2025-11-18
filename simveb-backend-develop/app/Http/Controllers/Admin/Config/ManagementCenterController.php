<?php

namespace App\Http\Controllers\Admin\Config;

use App\Consts\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManagementCenterRequest;
use App\Models\Config\ManagementCenter;
use App\Repositories\ManagementCenterRepository;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class ManagementCenterController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly ManagementCenterRepository $managementCenterRepository)
    {
        $this->initRepository(ManagementCenter::class);
        $this->authorizeResource(ManagementCenter::class, 'management_center');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, [
            'state:id,name',
            'village:id,name',
            'district:id,name',
            'town:id,name',
            'zones:id,name',
            'services:id,name',
            'type:id,name',
            'responsible:id,identity_id' => ['identity:id,firstname,lastname,telephone'],
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response($this->managementCenterRepository->create());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function edit(ManagementCenter $managementCenter)
    {
        $relations = [
            'services' => $managementCenter->services()->pluck('id')->toArray(),
            'zones' => $managementCenter->zones()->pluck('id')->toArray(),
            'parks' => $managementCenter->parks()->pluck('id')->toArray(),
        ];
        $managementCenter = $managementCenter->toArray() + $relations;


        return response([
            'management_center' => $managementCenter
            ] + $this->managementCenterRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     * @param ManagementCenterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function store(ManagementCenterRequest $request)
    {
        return response($this->managementCenterRepository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     * @param ManagementCenter $managementCenter
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function show(ManagementCenter $managementCenter)
    {
        return response($managementCenter->load($managementCenter::relations()));
    }


    /**
     * Update the specified resource in storage.
     * @param ManagementCenterRequest $request
     * @param ManagementCenter $managementCenter
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function update(ManagementCenterRequest $request, ManagementCenter $managementCenter)
    {
        return response($this->managementCenterRepository->update($managementCenter, $request->validated(), $request));
    }

    /**
     * Remove the specified resource from storage.
     * @param ManagementCenter $managementCenter
     */
    public function destroy(ManagementCenter $managementCenter)
    {
        $managementCenter->services()->detach();
        $managementCenter->ownerTypes()->detach();
        $managementCenter->parks()->detach();

        return response($this->repository->destroy($managementCenter));
    }
}
