<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\PledgeLift;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PledgeLiftPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('browse-'.Str::kebab('PledgeLift'));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('show-'.Str::kebab('PledgeLift'));
    }

    /**
     * @param User $user
     * @param PledgeLift $pledgeLift
     * @return mixed
     */
    public function update(User $user, PledgeLift $pledgeLift)
    {
        return $user->onlineProfile->hasPermissionTo('update-'.Str::kebab('PledgeLift'));
    }

    /**
     * @param User $user
     * @param PledgeLift $pledgeLift
     * @return mixed
     */
    public function delete(User $user, PledgeLift $pledgeLift)
    {
        return $user->onlineProfile->hasPermissionTo('delete-'.Str::kebab('PledgeLift'));
    }

    /**
     * @param User $user
     * @param PledgeLift $pledgeLift
     * @return mixed
     */
    public function forceDelete(User $user, PledgeLift $pledgeLift)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-'.Str::kebab('PledgeLift'));
    }
}
