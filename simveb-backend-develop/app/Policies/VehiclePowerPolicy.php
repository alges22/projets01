<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Vehicle\VehiclePower;

class VehiclePowerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-vehicle-power');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehiclePower $vehiclePower): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-vehicle-power');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-vehicle-power');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehiclePower $vehiclePower): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-vehicle-power');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehiclePower $vehiclePower): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-vehicle-power');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehiclePower $vehiclePower): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehiclePower $vehiclePower): void
    {
        //
    }
}
