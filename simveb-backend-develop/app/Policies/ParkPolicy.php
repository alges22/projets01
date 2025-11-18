<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Park;

class ParkPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-park');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Park $park): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-park');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-park');
    }

    public function update(User $user ,Park $park): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-park');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Park $park): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-park');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Park $park): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-park');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Park $park): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-park');
    }
}
