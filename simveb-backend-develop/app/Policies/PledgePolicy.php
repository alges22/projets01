<?php

namespace App\Policies;

use App\Models\Account\User;
use App\Models\Pledge;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PledgePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('browse-'.Str::kebab('Pledge'));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->onlineProfile->hasPermissionTo('show-'.Str::kebab('Pledge'));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->onlineProfile->hasAnyPermission([
            'store-'.Str::kebab('PledgeByDistributor'),
            'store-'.Str::kebab('PledgeByBank'),
        ]);
    }

    /**
     * @param User $user
     * @param Pledge $pledge
     * @return mixed
     */
    public function update(User $user, Pledge $pledge)
    {
        return $user->onlineProfile->hasPermissionTo('update-'.Str::kebab('Pledge'));
    }

    /**
     * @param User $user
     * @param Pledge $pledge
     * @return mixed
     */
    public function delete(User $user, Pledge $pledge)
    {
        return $user->onlineProfile->hasPermissionTo('delete-'.Str::kebab('Pledge'));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function validate(User $user)
    {
        return $user->onlineProfile->hasAnyPermission([
            'validate-pledge-by-anatt',
            'validate-pledge-by-justice',
            'validate-pledge-by-institution',
        ]);
    }

    public function reject(User $user)
    {
        return $user->onlineProfile->hasAnyPermission([
            'reject-pledge-by-anatt',
            'reject-pledge-by-justice',
            'reject-pledge-by-institution',
        ]);
    }

    /**
     * @param User $user
     * @param Pledge $pledge
     * @return mixed
     */
    public function restore(User $user, Pledge $pledge)
    {
        return $user->onlineProfile->hasPermissionTo('restore-'.Str::kebab('Pledge'));
    }


    public function forceDelete(User $user, Pledge $pledge)
    {
        return $user->onlineProfile->hasPermissionTo('force-delete-'.Str::kebab('Pledge'));
    }

    /**
     * @param User $user
     * @param Pledge $pledge
     * @return mixed
     */
    public function lift(User $user, Pledge $pledge)
    {
        return $user->onlineProfile->hasPermissionTo('lift-'.Str::kebab('Pledge'));
    }

}
