<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Vehicle\VehicleCharacteristic;

class VehicleCharacteristicPolicy
{
    /*
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-vehicle-characteristic');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleCharacteristic $vehicleCharacteristic): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-vehicle-characteristic');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-vehicle-characteristic');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleCharacteristic $vehicleCharacteristic): bool
    {
        return  $user->onlineProfile->hasPermissionTo('update-vehicle-characteristic');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleCharacteristic $vehicleCharacteristic): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-vehicle-characteristic');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehicleCharacteristic $vehicleCharacteristic): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-vehicle-characteristic');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehicleCharacteristic $vehicleCharacteristic): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-vehicle-characteristic');
    }
}
