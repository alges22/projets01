<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accreditation\AccreditationRequest;
use App\Http\Requests\Accreditation\ValidateOrRejectAccreditationRequest;
use App\Models\Accreditation;
use App\Repositories\AccreditationRepository;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Http\Request;

class AccreditationController extends Controller
{
    public function __construct(private readonly AccreditationRepository $repository)
    {
        $this->authorizeResource(Accreditation::class);
        $this->middleware('permission:store-accreditation')->only('userProfiles');
        $this->middleware('permission:validate-accreditation')->only('accredit');
        $this->middleware('permission:reject-accreditation')->only('reject');
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
    public function create(Request $request)
    {
        return response($this->repository->create($request->validate(['profile_id' => 'required|uuid|exists:profiles,id'])));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccreditationRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Accreditation $accreditation)
    {
        return response($accreditation->load(Accreditation::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccreditationRequest $request, Accreditation $accreditation)
    {
        return response($this->repository->update($accreditation, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Accreditation $accreditation)
    {
        return response($this->repository->destroy($accreditation));
    }

    /**
     *
     */
    public function userProfiles(Request $request)
    {
        return response($this->repository->getUserProfiles($request->validate(['npi' => ['required', new ValideNpiRule, 'exists:users,username']])));
    }

    /**
     *
     */
    public function accredit(ValidateOrRejectAccreditationRequest $request)
    {
        return response($this->repository->validate($request->validated()));
    }

    /**
     *
     */
    public function reject(ValidateOrRejectAccreditationRequest $request)
    {
        return response($this->repository->reject($request->validated()));
    }
}
