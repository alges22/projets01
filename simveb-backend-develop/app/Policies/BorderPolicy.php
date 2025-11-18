<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Border;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class BorderPolicy
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
        return $user->onlineProfile->hasPermissionTo('browse-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Border  $border
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Border $border)
    {
        return $user->onlineProfile->hasPermissionTo('show-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('store-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Border  $border
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Border $border)
    {
        return $user->onlineProfile->hasPermissionTo('update-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Border  $border
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Border $border)
    {
        return $user->onlineProfile->hasPermissionTo('delete-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('validate-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('reject-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Border  $border
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Border $border)
    {
        return $user->onlineProfile->hasPermissionTo('restore-' . Str::kebab('Border'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Border  $border
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Border $border)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-' . Str::kebab('Border'));
    }
}
