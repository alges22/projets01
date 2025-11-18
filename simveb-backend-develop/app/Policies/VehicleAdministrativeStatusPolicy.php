<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Vehicle\VehicleAdministrativeStatus;

class VehicleAdministrativeStatusPolicy
{
    /**
     * Determine whether the user can view any models.
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('browse-vehicle-administrative-status');
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param VehicleAdministrativeStatus $vehicleAdministrativeStatus
     * @return bool
     */
    public function view(User $user, VehicleAdministrativeStatus $vehicleAdministrativeStatus): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-vehicle-administrative-status');
    }

    /**
     * Determine whether the user can restore the model.
     * @param User $user
     * @param VehicleAdministrativeStatus $vehicleAdministrativeStatus
     * @return Void
     */
    public function restore(User $user, VehicleAdministrativeStatus $vehicleAdministrativeStatus): Void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @param User $user
     * @param VehicleAdministrativeStatus $vehicleAdministrativeStatus
     * @return Void
     */
    public function forceDelete(User $user, VehicleAdministrativeStatus $vehicleAdministrativeStatus): Void
    {
        //
    }
}
