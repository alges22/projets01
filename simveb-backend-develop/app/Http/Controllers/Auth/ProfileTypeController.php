<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileType\ToggleProfileTypeMemberStatusRequest;
use App\Http\Requests\Auth\ProfileType\UpdatePlateColorRequest;
use App\Models\Auth\ProfileType;
use App\Models\Plate\PlateColor;
use App\Repositories\Auth\ProfileTypeRepository;
use App\Traits\CrudRepositoryTrait;

class ProfileTypeController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly ProfileTypeRepository $profileTypeRepository)
    {
        $this->initRepository(ProfileType::class);
        $this->authorizeResource(ProfileType::class);
        // $this->middleware('permission:update-profile-type')->only(['updatePlateColors', 'toggleMemberStatus']);
         $this->middleware('permission:browse-space-staff')->only(['getMembers']);
    }

    public function index()
    {
        return response($this->repository->getAll());
    }

    public function show(ProfileType $profileType)
    {
        return response($profileType->load($profileType::relations()));
    }

    public function create()
    {
        return $this->successResponse([
            'plate_colors' => PlateColor::select(['id', 'name', 'label', 'color_code', 'text_color', 'cost'])->get(),
        ]);
    }

    public function updatePlateColors(ProfileType $profileType, UpdatePlateColorRequest $request)
    {
        return $this->successResponse($this->profileTypeRepository->updatePlateColors($profileType, $request->validated()));
    }

    public function getMembers()
    {
        return response($this->profileTypeRepository->getMembers());
    }

    public function toggleMemberStatus(ToggleProfileTypeMemberStatusRequest $request)
    {
        return response($this->profileTypeRepository->toggleMemberStatus($request->validated()));
    }
}
