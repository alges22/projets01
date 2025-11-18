<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\ManagementCenter;

class ManagementCenterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-management-center');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ManagementCenter $managementCenter): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-management-center');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-management-center');
    }

    public function update(User $user ,ManagementCenter $managementCenter): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-management-center');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ManagementCenter $managementCenter): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-management-center');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ManagementCenter $managementCenter): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-management-center');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ManagementCenter $managementCenter): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-management-center');
    }
}
