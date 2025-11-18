<?php

namespace Ntech\UserPackage\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role as ModelsRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;
use Ntech\UserPackage\Repositories\RoleRepository;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleRepository $roleRepository
    ){}

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        return response($this->roleRepository->getList());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return response([
            'modules' => $this->roleRepository->getModules(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param RoleRepository $roleRepository
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request,RoleRepository $roleRepository)
    {
        $this->validate($request,[
            'name' => ['required','string','unique:roles,name'],
            'label' => ['string','required'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        return response($roleRepository->store($request->only(['name','label','permissions'])));
    }

    /**
     * @param Role $role
     * @return Application|Redirector|RedirectResponse
     */
    public function show(Role $role)
    {
        return response($role->load(['permissions']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     * @return Application|Factory|View
     */
    public function edit(Role $role)
    {
        return response([
            'modules' => $this->roleRepository->getModules(),
            "role" => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Role $role
     * @param RoleRepository $roleRepository
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, Role $role,RoleRepository $roleRepository)
    {
        $this->validate($request,[
            'name' => ['required','string',Rule::unique("roles","name")->ignore($role->id)],
            'label' => ['string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        return response($roleRepository->update($role,$request->only(['name','label','permissions'])));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return Response
     */
    public function destroy(ModelsRole $role)
    {
        return  response($role->secureDelete(['users']));
    }
}
