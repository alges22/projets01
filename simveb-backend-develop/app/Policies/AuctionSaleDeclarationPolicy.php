<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Auction\AuctionSaleDeclaration;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class AuctionSaleDeclarationPolicy
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
        return $user->onlineProfile->hasPermissionTo('browse-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\AuctionSaleDeclaration  $auctionSaleDeclaration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return $user->onlineProfile->hasPermissionTo('show-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('store-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\AuctionSaleDeclaration  $auctionSaleDeclaration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return $user->onlineProfile->hasPermissionTo('update-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\AuctionSaleDeclaration  $auctionSaleDeclaration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return $user->onlineProfile->hasPermissionTo('delete-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function validate(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('validate-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Account\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('reject-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\AuctionSaleDeclaration  $auctionSaleDeclaration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return $user->onlineProfile->hasPermissionTo('restore-' . Str::kebab('AuctionSaleDeclaration'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Account\User  $user
     * @param  \App\Models\AuctionSaleDeclaration  $auctionSaleDeclaration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-' . Str::kebab('AuctionSaleDeclaration'));
    }
}
