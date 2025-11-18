<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\ReimmatriculationReason;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class ReimmatriculationReasonPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\ReimmatriculationReason  $reimmatriculationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ReimmatriculationReason $reimmatriculationReason)
    {
        return $user->onlineProfile->hasPermissionTo('show-' . Str::kebab('ReimmatriculationReason'));
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('store-' . Str::kebab('ReimmatriculationReason'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\ReimmatriculationReason  $reimmatriculationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ReimmatriculationReason $reimmatriculationReason)
    {
        return $user->onlineProfile->hasPermissionTo('update-' . Str::kebab('ReimmatriculationReason'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\ReimmatriculationReason  $reimmatriculationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ReimmatriculationReason $reimmatriculationReason)
    {
        return $user->onlineProfile->hasPermissionTo('delete-' . Str::kebab('ReimmatriculationReason'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('validate-' . Str::kebab('ReimmatriculationReason'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('reject-' . Str::kebab('ReimmatriculationReason'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\ReimmatriculationReason  $reimmatriculationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ReimmatriculationReason $reimmatriculationReason)
    {
        return $user->onlineProfile->hasPermissionTo('restore-' . Str::kebab('ReimmatriculationReason'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\ReimmatriculationReason  $reimmatriculationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ReimmatriculationReason $reimmatriculationReason)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-' . Str::kebab('ReimmatriculationReason'));
    }
}
