<?php

namespace App\Http\Controllers;

use App\Http\Requests\PledgeLiftRequest;
use App\Models\Pledge;
use App\Models\PledgeLift;
use App\Repositories\PledgeLiftRepository;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Request;
use App\Enums\Status;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PledgeLiftController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly PledgeLiftRepository $pledgeLiftRepository)
    {
        $this->initRepository(PledgeLift::class);
        $this->authorizeResource(PledgeLift::class);

        $this->middleware('permission:validate-pledge-lift-by-justice|validate-pledge-lift-by-anatt')->only('validateLift');
        $this->middleware('permission:reject-pledge-lift-by-justice|reject-pledge-lift-by-anatt')->only('rejectLift');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->pledgeLiftRepository->getAll(true, PledgeLift::relations()));
    }

    /**
     * Display the specified resource.
     */
    public function show(PledgeLift $pledgeLift)
    {
        return response($pledgeLift->load([
            'author:id,user_id,identity_id',
            'author.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'institutionEmitted:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment',
            'activeTreatment.institutionTreat:id,acronym,name,ifu,email,telephone,address',
            'activeTreatment.treatedBy.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToClerk.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'activeTreatment.affectedToAnatt.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge',
            'pledge.vehicle',
            'pledge.vehicleOwner',
            'pledge.author:id,user_id,identity_id',
            'pledge.author.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge.vehicleOwner.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge.institutionEmitted:id,acronym,name,ifu,email,telephone,address',
            'pledge.financialInstitution:id,acronym,name',
            'pledge.activeTreatment',
            'pledge.activeTreatment.institutionTreat:id,acronym,name,ifu,email,telephone,address',
            'pledge.activeTreatment.treatedBy.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge.activeTreatment.affectedToInstitution.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge.activeTreatment.affectedToClerk.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
            'pledge.activeTreatment.affectedToAnatt.identity:id,firstname,lastname,email,telephone,npi,ifu,birthdate',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PledgeLiftRequest $request, PledgeLift $pledgeLift)
    {
        return response($this->pledgeLiftRepository->update($pledgeLift, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PledgeLift $pledgeLift)
    {
        if ($pledgeLift->status === Status::emitted->name)
        {
            return response($this->repository->destroy($pledgeLift));
        }else{
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible de supprimer, dossier en cours de traitement");
        }
    }

    /**
     * @param PledgeLift $pledgeLift
     * @param $validate
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function validateLift(PledgeLiftRequest $resquest, PledgeLift $pledgeLift)
    {
        return response($this->pledgeLiftRepository->validatePledgeLift($pledgeLift, $resquest->validated()));
    }

    /**
     * @param PledgeLift $pledgeLift
     * @param Request $request
     * @param $reject
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function rejectLift(PledgeLiftRequest $request, PledgeLift $pledgeLift)
    {
        return response($this->pledgeLiftRepository->rejectLift($pledgeLift, $request->validated()));
    }
}
