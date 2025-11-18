<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Config\Village;
use App\Models\Order\Order;

class AdminOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('browse-order');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        //
        return $user->onlineProfile->hasPermissionTo('show-order');
    }
}
