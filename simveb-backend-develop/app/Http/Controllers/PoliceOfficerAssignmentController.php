<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoliceOfficerAssignment\PoliceOfficerAssignmentRequest;
use App\Http\Requests\PoliceOfficerAssignment\ValidateOrRejectPoliceOfficerAssignmentRequest;
use App\Models\PoliceOfficer\PoliceOfficerAssignment;
use App\Repositories\PoliceOfficerAssignmentRepository;

class PoliceOfficerAssignmentController extends Controller
{
    public function __construct(private readonly PoliceOfficerAssignmentRepository $repository)
    {
        $this->authorizeResource(PoliceOfficerAssignment::class, 'assignment');
        $this->middleware('permission:validate-police-officer-assignment')->only('assign');
        $this->middleware('permission:reject-police-officer-assignment')->only('reject');
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
    public function store(PoliceOfficerAssignmentRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(PoliceOfficerAssignment $assignment)
    {
        return response($assignment->load($assignment::relations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PoliceOfficerAssignmentRequest $request, PoliceOfficerAssignment $assignment)
    {
        return response($this->repository->update($assignment, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PoliceOfficerAssignment $assignment)
    {
        return response($this->repository->destroy($assignment));
    }

    /**
     *
     */
    public function assign(ValidateOrRejectPoliceOfficerAssignmentRequest $request)
    {
        return response($this->repository->validate($request->validated()));
    }

    /**
     *
     */
    public function reject(ValidateOrRejectPoliceOfficerAssignmentRequest $request)
    {
        return response($this->repository->reject($request->validated()));
    }
}
