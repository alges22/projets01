<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Plate\PlateOrder;

class PlateOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('browse-plate-order');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlateOrder $plateOrder): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-plate-order');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-plate-order');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlateOrder $plateOrder): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlateOrder $plateOrder): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlateOrder $plateOrder): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlateOrder $plateOrder): bool
    {
        //
    }
}
