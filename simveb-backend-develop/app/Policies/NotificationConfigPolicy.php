<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\NotificationConfig;

class NotificationConfigPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->onlineProfile->hasPermissionTo('browse-notification-config');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NotificationConfig $notificationConfig): bool
    {
        return $user->onlineProfile->hasPermissionTo('show-notification-config');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NotificationConfig $notificationConfig): bool
    {
        return $user->onlineProfile->hasPermissionTo('update-notification-config');
    }
}
