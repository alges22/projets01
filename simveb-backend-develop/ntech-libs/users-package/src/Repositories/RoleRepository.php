<?php

namespace Ntech\UserPackage\Repositories;

use App\Models\Auth\Role as AuthRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Ntech\UserPackage\Models\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository
{

    public function getList(): Collection|array
    {
        return AuthRole::query()
            ->filter()
            ->get();
    }

    function store(array $data)
    {
        $role =  Role::query()->create(
            [
                "name" => $data['name'],
                "label" => $data['label'],
                "editable" => true
            ]
        );

        if (isset($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    /**
     * @param Role | Model $role
     * @param array $data
     * @return Role
     */
    function update(Role|Model $role, array $data): Role
    {
        $role->update(
            [
                "name" => $data['name'],
                "label" => $data['label'],
            ]
        );

        if (isset($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return $role->refresh();
    }

    function delete(Role|Model $role)
    {
        $role->secureDelete(["permissions"],);

        return $role;
    }

    public function getModules()
    {
        return Module::query()
            ->with("permissions")
            ->filter()
            ->orderBy('name')
            ->get();
    }
}
