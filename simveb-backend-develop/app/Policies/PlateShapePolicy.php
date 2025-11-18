<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Plate\PlateShape;

class PlateShapePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-plate-shape');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlateShape $plateShape): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-plate-shape');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->onlineProfile->hasPermissionTo('store-plate-shape');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlateShape $plateShape): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-plate-shape');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlateShape $plateShape): bool
    {
        return $user->onlineProfile->hasPermissionTo('delete-plate-shape');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlateShape $plateShape): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlateShape $plateShape): void
    {
        //
    }
}
