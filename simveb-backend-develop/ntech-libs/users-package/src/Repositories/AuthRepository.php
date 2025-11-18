<?php

namespace Ntech\UserPackage\Repositories;

use App\Enums\Status;
use App\Models\Account\User;
use App\Models\Auth\Invitation;

class AuthRepository
{
    public function retrieveUser(User | \Illuminate\Foundation\Auth\User $user): array
    {
        $data['user'] = $user->load([
            'identity',
            'roles',
            'onlineProfile:id,user_id,space_id,institution_id,type_id,suspended',
            'onlineProfile' => ['space:id,profile_type_id,template', 'institution:id,name,telephone,email,ifu'],
            'onlineProfile.type:id,code,name',
            'profiles:id,user_id,space_id,institution_id,type_id,suspended',
            'profiles' => [
                'space' => fn ($q) => $q->where('status', Status::active->name),
                'institution:id,name,telephone,email,ifu'
            ],
            'profiles.type:id,code,name'
        ]);
        $data['roles'] = $user->onlineProfile->roles;
        $data['permissions'] = $this->getProfilePermissions($user->onlineProfile);
        // $data['staff'] = $user->identity?->staff;
        $data['invitations'] = Invitation::where(['npi' => $user->username])->select(['id', 'npi', 'space_id', 'profile_type_id', 'status', 'accepted', 'denied', 'created_at'])->with(['space:id,institution_id,profile_type_id', 'space.institution:id,name,logo_path', 'space.profileType:id,code,name', 'profileType:id,code,name',])->get();
        $data['user']['pending_invitations_count'] = $user->pending_invitations_count;

        return $data;
    }

    public function getProfilePermissions($profile)
    {
        $permissions = [];

        foreach ($profile->roles as $role) {
            $permissions = array_merge($permissions, $role->permissions()->pluck("name")->toArray());
        }
        $permissions = array_merge($permissions, $profile->permissions()->pluck("name")->toArray());

        return $permissions;
    }

    public function getUserPermissions($user)
    {
        $permissions = [];

        foreach ($user->profiles as $profile) {
            foreach ($profile->roles as $role) {
                $permissions = array_merge($permissions, $role->permissions()->pluck("name")->toArray());
            }
        }

        return $permissions;
    }

    public function details(User | \Illuminate\Foundation\Auth\User $user): array
    {
        if ($user->hasRole(['operator'])) {
            return [
                'user' => $user->load(['identity']),
                'wallets' => [
                    'wallet' => $user->identity->operator->wallet,
                    'commissionWallet' => $user->identity->operator->commissionWallet,
                    'bonusWallet' => $user->identity->operator->bonusWallet,
                ]
            ];
        } elseif ($user->hasRole(['customer'])) {
            return [
                'user' => $user->load(['identity.customer']),
            ];
        } else {
            return [
                'user' => $user->load(['identity']),
            ];
        }
    }
}
