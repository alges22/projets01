<?php

namespace App\Http\Controllers;

use App\Http\Requests\PledgeLiftRequest;
use App\Http\Requests\PledgeResquest;
use App\Http\Requests\ValidatePledgeRequest;
use App\Models\Pledge;
use App\Enums\Status;
use App\Services\PledgeService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Repositories\PledgeRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use function App\Repositories\Vehicle\validate;

class PledgeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly PledgeRepository $pledgeRepository, private readonly PledgeService $service)
    {
        $this->initRepository(Pledge::class);
        $this->authorizeResource(Pledge::class);

        $this->middleware('permission:browse-pledge')->only('index');
        $this->middleware('permission:store-pledge-by-distributor|store-pledge-by-bank')->only('store');
        $this->middleware('permission:update-pledge')->only('update');
        $this->middleware('permission:show-pledge')->only('show');
        $this->middleware('permission:delete-pledge')->only('destroy');
        $this->middleware('permission:validate-pledge-by-institution|validate-pledge-by-justice|validate-pledge-by-anatt')->only('validate');
        $this->middleware('permission:reject-pledge-by-institution|reject-pledge-by-justice|reject-pledge-by-anatt')->only('reject');
        $this->middleware('permission:lift-pledge')->only('liftPledge');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->pledgeRepository->getAll(true, Pledge::relations()));
    }

    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        return response($this->pledgeRepository->create());
    }

    /**
     * Store a newly created resource in storage.
     * @param PledgeResquest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function store(PledgeResquest $request)
    {
        return response($this->pledgeRepository->store($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     * @param Pledge $pledge
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function show(Pledge $pledge)
    {
        return response($pledge->load(Pledge::relations()));
    }

    public function showVehicleAndOwnerByVin()
    {
        return response($this->pledgeRepository->showVehicleAndOwnerByVin());
    }

    /**
     * Update the specified resource in storage.
     * @param PledgeResquest $request
     * @param Pledge $pledge
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function update(PledgeResquest $request, Pledge $pledge)
    {
        return response($this->pledgeRepository->update($pledge, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pledge $pledge)
    {
        if ($pledge->status === Status::emitted->name)
        {
            return response($this->repository->destroy($pledge));
        }else{
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible de supprimer, car véhicule sous gage");
        }
    }

    /**
     * @param Pledge $pledge
     * @param $validate
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function validatePledge(PledgeResquest $resquest, Pledge $pledge)
    {
        $this->authorize('validate', $pledge);
        return response($this->pledgeRepository->validatePledge($pledge, $resquest->validated()));
    }

    /**
     * @param Pledge $pledge
     * @param $lift
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function liftPledge(PledgeLiftRequest $request, Pledge $pledge)
    {
        $this->authorize('lift', $pledge);
        return response($this->pledgeRepository->liftPledge($pledge, $request->validated(), $request));
    }

    /**
     * @param Pledge $pledge
     * @param Request $request
     * @param $reject
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function reject(PledgeResquest $request, Pledge $pledge)
    {
        $this->authorize('reject', $pledge);
        return response($this->pledgeRepository->reject($pledge, $request->validated()));
    }

    public function getClerkByCourt()
    {
        return response($this->pledgeRepository->getClerkByCourt());
    }

    /**
     * @param Pledge $pledge
     * @param PledgeResquest $resquest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function affectationToClerk(Pledge $pledge, PledgeResquest $resquest)
    {
        return response($this->service->affectationToClerk($pledge, $resquest->validated()));
    }

    public function totalPledges()
    {
        return response($this->pledgeRepository->totalPledges());
    }

    public function activeClosePledges()
    {
        return response($this->pledgeRepository->activeClosePledges());
    }

    public function listLiftablePledges()
    {
        return response($this->pledgeRepository->listLiftablePledges(true, Pledge::relations()));
    }
}
