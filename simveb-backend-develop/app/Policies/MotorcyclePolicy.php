<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Motorcycle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class MotorcyclePolicy
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
        return $user->onlineProfile->hasPermissionTo('browse-' . Str::kebab('Motorcycle'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Motorcycle  $motorcycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Motorcycle $motorcycle)
    {
        return $user->onlineProfile->hasPermissionTo('show-' . Str::kebab('Motorcycle'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('store-' . Str::kebab('Motorcycle'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Motorcycle  $motorcycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Motorcycle $motorcycle)
    {
        return $user->onlineProfile->hasPermissionTo('update-' . Str::kebab('Motorcycle'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Motorcycle  $motorcycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Motorcycle $motorcycle)
    {
        return $user->onlineProfile->hasPermissionTo('delete-' . Str::kebab('Motorcycle'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Motorcycle  $motorcycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Motorcycle $motorcycle)
    {
        return $user->onlineProfile->hasPermissionTo('restore-' . Str::kebab('Motorcycle'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Motorcycle  $motorcycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Motorcycle $motorcycle)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-' . Str::kebab('Motorcycle'));
    }
}
