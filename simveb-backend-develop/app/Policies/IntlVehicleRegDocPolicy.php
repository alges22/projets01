<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\IntlVehicleRegDoc;

class IntlVehicleRegDocPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-international-vehicle-registration-document');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IntlVehicleRegDoc $internationalVehicleRegistrationDocument): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-international-vehicle-registration-document');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-international-vehicle-registration-document');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IntlVehicleRegDoc $internationalVehicleRegistrationDocument): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-international-vehicle-registration-document');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IntlVehicleRegDoc $internationalVehicleRegistrationDocument): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-international-vehicle-registration-document');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IntlVehicleRegDoc $internationalVehicleRegistrationDocument): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IntlVehicleRegDoc $internationalVehicleRegistrationDocument): void
    {
        //
    }
}
