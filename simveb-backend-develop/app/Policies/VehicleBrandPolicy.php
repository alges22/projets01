<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Vehicle\VehicleBrand;

class VehicleBrandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-vehicle-brand');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleBrand $vehicleBrand): bool
    {
        return $user->onlineProfile->hasPermissionTo('browse-vehicle-brand');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-vehicle-brand');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleBrand $vehicleBrand): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-vehicle-brand');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleBrand $vehicleBrand): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-vehicle-brand');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehicleBrand $vehicleBrand): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehicleBrand $vehicleBrand): void
    {
        //
    }
}
