<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\District;

class DistrictPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('browse-district');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, District $district): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-district');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-district');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, District $district): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-district');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, District $district): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-district');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, District $district): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, District $district): void
    {
    }
}
