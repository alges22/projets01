<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeographicalAreaRequest;
use App\Models\Account\User;
use App\Models\Config\GeographicalArea;
use App\Traits\CrudRepositoryTrait;

class GeographicalAreaController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(GeographicalArea::class);
        $this->authorizeResource(GeographicalArea::class); //check if user has permission
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeographicalAreaRequest $request)
    {
        return response($this->repository->store([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'type' => $request->validated('type'),
            'code' => $request->validated('code'),
            'authorized_registration_format' => $request->validated('authorized_registration_format'),
            'validation_criteria' => $request->validated('validation_criteria'),
            'restrictions_or_special_requirements' => $request->validated('restrictions_or_special_requirements'),
            'staff_ids' => $request->validated('staff_ids'),
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(GeographicalArea $geographicalArea)
    {
        return response($geographicalArea->load($geographicalArea::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeographicalArea $geographicalArea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeographicalAreaRequest $request, GeographicalArea $geographicalArea)
    {
        return response($this->repository->update($geographicalArea, [
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'type' => $request->validated('type'),
            'code' => $request->validated('code'),
            'authorized_registration_format' => $request->validated('authorized_registration_format'),
            'validation_criteria' => $request->validated('validation_criteria'),
            'restrictions_or_special_requirements' => $request->validated('restrictions_or_special_requirements'),
            'staff_ids' => $request->validated('staff_ids'),
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeographicalArea $geographicalArea)
    {
        return response($this->repository->destroy($geographicalArea));
    }

    public function getStaff(GeographicalAreaRequest $request)
    {
        $staff = [];
        $geographicalArea = GeographicalArea::where('id', $request->id)->get()->first();

        foreach ($geographicalArea->staff_ids as $userID) {
            $user = User::where('id', $userID)->get();
            if ($user) {
                $staff[] = $user->first();
            }
        }

        return Response($staff);
    }
}
