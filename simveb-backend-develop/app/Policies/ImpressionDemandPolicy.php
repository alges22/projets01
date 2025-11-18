<?php
namespace App\Policies;

use App\Models\Account\User;
use App\Models\ImpressionDemand;

class ImpressionDemandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('browse-impression-demand');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImpressionDemand $demand): bool
    {
        return  $user->onlineProfile->hasPermissionTo('show-impression-demand');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->onlineProfile->hasPermissionTo('store-impression-demand');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImpressionDemand $impressionDemand): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImpressionDemand $demand): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImpressionDemand $demand): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImpressionDemand $demand): bool
    {
        //
    }
}
