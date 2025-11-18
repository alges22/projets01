<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\PaymentProvider;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class PaymentProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User  $user): bool
    {
        return $user->onlineprofile->hasPermissionTo('browse-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\PaymentProvider  $paymentProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PaymentProvider $paymentProvider)
    {
        return $user->onlineprofile->hasPermissionTo('show-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->onlineprofile->hasPermissionTo('store-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\PaymentProvider  $paymentProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PaymentProvider $paymentProvider)
    {
        return $user->onlineprofile->hasPermissionTo('update-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\PaymentProvider  $paymentProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PaymentProvider $paymentProvider)
    {
        return $user->onlineprofile->hasPermissionTo('delete-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->onlineprofile->hasPermissionTo('validate-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->onlineprofile->hasPermissionTo('reject-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewStats(User $user)
    {
        return $user->onlineprofile->hasPermissionTo('view-stats-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\PaymentProvider  $paymentProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PaymentProvider $paymentProvider)
    {
        return $user->onlineprofile->hasPermissionTo('restore-' . Str::kebab('PaymentProvider'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\Config\PaymentProvider  $paymentProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PaymentProvider $paymentProvider)
    {
        return $user->onlineprofile->hasPermissionTo('force-delete-' . Str::kebab('PaymentProvider'));
    }
}
