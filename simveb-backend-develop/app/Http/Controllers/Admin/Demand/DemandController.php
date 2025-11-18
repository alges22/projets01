<?php

namespace App\Http\Controllers\Admin\Demand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Immatriculation\AnattImmatriculationValidationRequest;
use App\Http\Resources\AdminDemandResource;
use App\Http\Resources\InterpoleDemandResource;
use App\Models\Order\Demand;
use App\Repositories\Demand\DemandRepository;

class DemandController extends Controller
{
    public function __construct(private DemandRepository $repository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll()->paginate(request()->input('per_page', 15)));
    }

    public function interpolDemands()
    {
        return response($this->repository->getInterpolDemands());
    }

    public function myPendingDemands()
    {
        return response($this->repository->myPendingDemands());
    }

    public function myTreatedDemands()
    {
        return response($this->repository->myTreatedDemands());
    }

    public function viewAny()
    {
        return response($this->repository->getAllFiltered(true, Demand::relations()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Demand $demand)
    {
        return $this->successResponse(new AdminDemandResource($demand));
    }

    /**
     * Display the specified resource.
     */
    public function showInterpoleDemand(Demand $demand)
    {
        return $this->successResponse(new InterpoleDemandResource($demand));
    }


    public function anattValidation(AnattImmatriculationValidationRequest $request)
    {
        if (!auth()->user()->hasPermissionTo('control-anatt-im-demand')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'effectuer cette action."
            ], 401);
        }
        return response($this->repository->anattValidation($request->validated()));
    }

    public function validateUpdates(Demand $demand)
    {
        $validation_is_done = $this->repository->validateUpdates($demand->id);

        if (!$validation_is_done) {
            return response([
                "success" => false,
                "message" =>  "Tous les historiques de mise à jour ne sont pas validés sur cette demande."
            ], 400);
        }
        return response($validation_is_done);
    }

    /**
     * Display a listing of the resource.
     */
    public function excelExport()
    {
        return $this->repository->excelExport();
    }

    /**
     * Display a listing of the resource.
     */
    public function pdfExport()
    {
        return $this->repository->pdfExport();
    }
}
