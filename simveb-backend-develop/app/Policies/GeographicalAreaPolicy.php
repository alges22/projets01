<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\GeographicalArea;

class GeographicalAreaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-geographical-area');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GeographicalArea $geographicalArea): bool
    {
        return $user->onlineProfile->hasPermissionTo('browse-geographical-area');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-geographical-area');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GeographicalArea $geographicalArea): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-geographical-area');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GeographicalArea $geographicalArea): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-geographical-area');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GeographicalArea $geographicalArea): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GeographicalArea $geographicalArea): void
    {
        //
    }
}
