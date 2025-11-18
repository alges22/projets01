<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Town;

class TownPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('browse-town');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Town $Town): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('show-town');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('store-town');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Town $town): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('update-town');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Town $town): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('delete-town');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Town $town): Void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Town $town): Void
    {
        //
    }
}
