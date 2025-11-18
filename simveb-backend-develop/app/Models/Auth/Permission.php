<?php

namespace App\Models\Auth;

use Ntech\UserPackage\Models\Module;
use Spatie\Permission\Models\Permission as ModelPermission;

class Permission extends ModelPermission
{
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
