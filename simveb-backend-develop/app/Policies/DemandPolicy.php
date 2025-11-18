<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Order\Demand;

class DemandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('browse-demand');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Demand $demand): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-demand');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)//: bool
    {
        //return $user->onlineProfile->hasPermissionTo('store-demand');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Demand $demand)//: bool
    {
        //return $user->onlineProfile->hasPermissionTo('update-demand');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Demand $demand)//: bool
    {
        //return $user->onlineProfile->hasPermissionTo('delete-demand');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Demand $demand): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Demand $demand): void
    {
    }
}
