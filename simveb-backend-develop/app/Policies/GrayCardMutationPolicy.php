<?php

namespace App\Policies;

use App\Models\GrayCardMutation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GrayCardMutationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('browse-card-mutation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GrayCardMutation $grayCardMutation): bool
    {
        return $user->hasPermissionTo('show-card-mutation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('store-card-mutation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GrayCardMutation $grayCardMutation): bool
    {
        return $user->hasPermissionTo('update-card-mutation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GrayCardMutation $grayCardMutation): bool
    {
        return $user->hasPermissionTo('delete-card-mutation');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GrayCardMutation $grayCardMutation): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GrayCardMutation $grayCardMutation): void
    {
        //
    }
}
