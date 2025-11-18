<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Vehicle\VehicleEnergySource;

class VehicleEnergySourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-vehicle-energy-source');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleEnergySource $vehicleEnergySource): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-vehicle-energy-source');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-vehicle-energy-source');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleEnergySource $vehicleEnergySource): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-vehicle-energy-source');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleEnergySource $vehicleEnergySource): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-vehicle-energy-source');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehicleEnergySource $vehicleEnergySource): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehicleEnergySource $vehicleEnergySource): void
    {
        //
    }
}
