<?php

namespace Ntech\UserPackage\Repositories;

use App\Models\Module;
use App\Models\Permission;

class PermissionRepository
{
    public function getList()
    {
        return Permission::with(['module' => function ($query) {
            $query->groupBy('id')->orderBy('name', 'asc');
        }])->paginate();
    }

    public function getAll()
    {
        return Permission::with(['module' => function ($query) {
            $query->groupBy('id')->orderBy('name', 'asc');
        }])->get();
    }
}
