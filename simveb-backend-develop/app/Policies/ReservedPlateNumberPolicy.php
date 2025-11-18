<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\ReservedPlateNumber;

class ReservedPlateNumberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-reserved-plate-number');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReservedPlateNumber $reservedPlateNumber): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-reserved-plate-number');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-reserved-plate-number');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReservedPlateNumber $reservedPlateNumber): bool
    {
        return  $user->onlineProfile->hasPermissionTo('update-reserved-plate-number');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReservedPlateNumber $reservedPlateNumber): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-reserved-plate-number');

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReservedPlateNumber $reservedPlateNumber): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-reserved-plate-number');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReservedPlateNumber $reservedPlateNumber): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-reserved-plate-number');
    }
}
