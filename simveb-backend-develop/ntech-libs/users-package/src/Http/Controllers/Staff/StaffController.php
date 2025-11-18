<?php

namespace Ntech\UserPackage\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Config\ManagementCenter;
use App\Models\Config\Organization;
use App\Repositories\Crud\CrudRepository;
use App\Repositories\Demand\DemandRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ntech\UserPackage\Http\Requests\StaffRequest;
use Ntech\UserPackage\Http\Requests\StaffStatusRequest;
use Ntech\UserPackage\Models\Position;
use Ntech\UserPackage\Models\Staff;
use Ntech\UserPackage\Repositories\RoleRepository;
use Ntech\UserPackage\Repositories\StaffRepository;
use Ntech\ActivityLogPackage\Services\ActivityLogService;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffRepository $staffRepository,
        private readonly RoleRepository $roleRepository,
        private readonly DemandRepository $demandRepository,
        private readonly ActivityLogService $activityLogService
    ) {
        // $this->authorizeResource(Staff::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        return response($this->staffRepository->getAll());
    }

    public function search()
    {
        return response($this->staffRepository->search());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return response([
            'roles' => $this->roleRepository->getList(),
            'positions' => (new CrudRepository(Position::class))->getAll(false),
            'organizations' =>  Organization::query()->select(['id', 'name'])->get(),
            'centers' => ManagementCenter::query()->select(['id', 'name'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StaffRequest $request
     * @param StaffRepository $staffRepository
     * @return Application
     */
    public function store(StaffRequest $request, StaffRepository $staffRepository)
    {
        return response($staffRepository->createStaff($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param $staffId
     * @return Response
     */
    public function show($staffId)
    {
        $staff = $this->staffRepository->getStaffById($staffId);
        $treatedDemands = $staff->profile ? $this->demandRepository->getTreatedDemandsByProfile($staff->profile->id)->toArray() : [];
        $latestActivityLogs = $staff->profile ? $this->activityLogService->getLastLogsByProfile($staff->profile->id) : [];
        $staff->treatedDemands = $treatedDemands;
        $staff->latestActivityLogs = $latestActivityLogs;

        return response($staff);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $staffId
     * @return Application
     */
    public function edit($staffId)
    {
        $staff = $this->staffRepository->getStaffById($staffId);
        return response([
            'staff' => $staff,
            'roles' => $this->roleRepository->getList(),
            'positions' => (new CrudRepository(Position::class))->getAll(false),
            'organizations' =>  Organization::query()->select(['id', 'name'])->get(),
            'centers' => ManagementCenter::query()->select(['id', 'name'])->get(),
        ]);
    }

    public function updateStatus(Staff $staff, StaffStatusRequest $request)
    {
        return response((new StaffRepository)->updateStatus($staff, $request->validated()));
    }
    public function updateStaffCenter(Request $request)
    {
        $request->validate([
            'profile_id' => ['uuid', 'exists:profiles,id', 'required'],
            'center_id' => ['nullable', 'exists:management_centers,id']
        ]);

        return response((new StaffRepository)->updateStaffCenter($request->only(['profile_id', 'center_id'])));
    }

    public function updateStaffOrganization(Request $request)
    {
        $request->validate([
            'profile_id' => ['uuid', 'exists:profiles,id', 'required'],
            'organizations' => ['nullable', 'array', 'exists:organizations,id']
        ]);

        return response((new StaffRepository)->updateStaffOrganization($request->only(['profile_id', 'organizations'])));
    }

    public function destroy(Staff $staff)
    {
        return response($this->staffRepository->delete($staff));
    }
}
