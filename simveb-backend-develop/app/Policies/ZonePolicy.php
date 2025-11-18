<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Zone;

class ZonePolicy
{
     /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('browse-zone');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Zone $zone): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('show-zone');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('store-zone');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Zone $zone): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('update-zone');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Zone $zone): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('delete-zone');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Zone $zone): Void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Zone $zone): Void
    {
        //
    }
}
