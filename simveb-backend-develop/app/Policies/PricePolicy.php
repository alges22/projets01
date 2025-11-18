<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Price;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class PricePolicy
{
    use HandlesAuthorization;

    /*public function before(User $user, $ability)
    {
        if($user->isSuperAdmin()) {
            return true;
        }
    }*/

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('browse-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\Price  $price
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Price $price)
    {
        return $user->can('show-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('store-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\Price  $price
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Price $price)
    {
        return $user->can('update-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\Price  $price
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Price $price)
    {
        return $user->can('delete-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->can('validate-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->can('reject-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\Price  $price
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Price $price)
    {
        return $user->can('restore-' . Str::slug('Price'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\Price  $price
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Price $price)
    {
        return $user->can('force-delete-' . Str::slug('Price'));
    }
}
