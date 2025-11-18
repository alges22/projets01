<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\ManagementCenterType;

class ManagementCenterTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-management-center-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ManagementCenterType $managementCenterType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-management-center-type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-management-center-type');
    }

    public function update(User $user ,ManagementCenterType $managementCenterType): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-management-center-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ManagementCenterType $managementCenterType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-management-center-type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ManagementCenterType $managementCenterType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-management-center-type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ManagementCenterType $managementCenterType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-management-center-type');
    }
}
