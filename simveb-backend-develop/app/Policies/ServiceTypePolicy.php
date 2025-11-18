<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Service;
use App\Models\Config\ServiceType;

class ServiceTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-service-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceType $serviceType): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-service-type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-service-type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceType $serviceType): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-service-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceType $serviceType): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-service-type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceType $serviceType): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $serviceType): void
    {
        //
    }
}
