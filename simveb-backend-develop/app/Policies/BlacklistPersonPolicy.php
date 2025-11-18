<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\BlacklistPerson;

class BlacklistPersonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-blacklist-person');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BlacklistPerson $blacklistPerson): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-blacklist-person');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-blacklist-person');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BlacklistPerson $blacklistPerson): bool
    {
        return  $user->onlineProfile->hasPermissionTo('update-blacklist-person');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BlacklistPerson $blacklistPerson): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-blacklist-person');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BlacklistPerson $blacklistPerson): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-blacklist-person');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BlacklistPerson $blacklistPerson): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-blacklist-person');
    }
}
