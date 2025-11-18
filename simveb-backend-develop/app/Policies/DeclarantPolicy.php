<?php

namespace App\Policies;

use App\Models\Account\Declarant;
use App\Models\Account\User;

class DeclarantPolicy
{
    /**
     * Determine whether the user can view any models.
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-declarant');
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Declarant $declarant
     * @return bool
     */
    public function view(User $user, Declarant $declarant): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-declarant');
    }

    /**
     * Determine whether the user can create models.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-declarant');
    }

    public function update(User $user ,Declarant $declarant): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-declarant');
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Declarant $declarant
     * @return bool
     */
    public function delete(User $user, Declarant $declarant): bool
    {
        return  $user->onlineProfile->hasPermissionTo('delete-declarant');
    }

    /**
     * Determine whether the user can restore the model.
     * @param User $user
     * @param Declarant $declarant
     * @return bool
     */
    public function restore(User $user, Declarant $declarant): bool
    {
        return  $user->onlineProfile->hasPermissionTo('restore-declarant');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @param User $user
     * @param Declarant $declarant
     * @return bool
     */
    public function forceDelete(User $user, Declarant $declarant): bool
    {
        return  $user->onlineProfile->hasPermissionTo('force-delete-declarant');
    }
}
