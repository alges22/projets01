<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Vehicle\VehicleType;

class VehicleTypePolicy
{
    /*
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-vehicle-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleType $vehicleType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-vehicle-type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-vehicle-type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleType $vehicleType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('update-vehicle-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicletype $vehicleType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-vehicle-type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vehicletype $vehicletype): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-vehicle-type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehicleType $vehicleType): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-vehicle-type');
    }
}
