<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Order\Commission;

class CommissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-commission');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Commission $commission): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-commission');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-commission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Commission $commission): bool
    {
        return  $user->onlineProfile->hasPermissionTo('update-commission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Commission $commission): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-commission');

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Commission $commission): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-commission');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Commission $commission): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-commission');
    }
}
