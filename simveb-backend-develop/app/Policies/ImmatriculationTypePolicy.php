<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\ImmatriculationType;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class ImmatriculationTypePolicy
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
        return $user->onlineProfile->hasPermissionTo('browse-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\ImmatriculationType  $immatriculationType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ImmatriculationType $immatriculationType)
    {
        return $user->onlineProfile->hasPermissionTo('show-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('store-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\ImmatriculationType  $immatriculationType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ImmatriculationType $immatriculationType)
    {
        return $user->onlineProfile->hasPermissionTo('update-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\ImmatriculationType  $immatriculationType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ImmatriculationType $immatriculationType)
    {
        return $user->onlineProfile->hasPermissionTo('delete-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('validate-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('reject-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\ImmatriculationType  $immatriculationType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ImmatriculationType $immatriculationType)
    {
        return $user->onlineProfile->hasPermissionTo('restore-' . Str::kebab('ImmatriculationType'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\ImmatriculationType  $immatriculationType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ImmatriculationType $immatriculationType)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-' . Str::kebab('ImmatriculationType'));
    }
}
