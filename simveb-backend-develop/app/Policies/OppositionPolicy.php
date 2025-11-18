<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Opposition;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class OppositionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('browse-' . Str::kebab('Opposition'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Opposition  $opposition
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Opposition $opposition)
    {
        return $user->onlineProfile->hasPermissionTo('show-' . Str::kebab('Opposition'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */

    public function create(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('create-' . Str::kebab('Opposition'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Opposition  $opposition
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Opposition $opposition)
    {
        return $user->onlineProfile->hasPermissionTo('update-' . Str::kebab('Opposition'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Opposition  $opposition
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Opposition $opposition)
    {
        return $user->onlineProfile->hasPermissionTo('delete-' . Str::kebab('Opposition'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->onlineProfile->hasAnyPermission([
            'validate-opposition-by-clerk',
            'validate-opposition-by-judge'
        ]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->onlineProfile->hasAnyPermission([
            'reject-opposition-by-clerk',
            'reject-opposition-by-judge'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Opposition  $opposition
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Opposition $opposition)
    {
        return $user->onlineProfile->hasPermissionTo('restore-' . Str::kebab('Opposition'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Opposition  $opposition
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Opposition $opposition)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-' . Str::kebab('Opposition'));
    }

    public function lift(User $user, Opposition $opposition)
    {
        return $user->onlineProfile->hasPermissionTo('lift-'.Str::kebab('Opposition'));
    }
}
