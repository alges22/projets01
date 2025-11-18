<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Village;

class VillagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('browse-village');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Village $village): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('show-village');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('store-village');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Village $village): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('update-village');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Village $village): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('delete-village');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Village $village): Void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Village $village): Void
    {
        //
    }
}
