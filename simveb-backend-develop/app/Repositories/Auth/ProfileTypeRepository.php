<?php

namespace App\Repositories\Auth;

use App\Models\Auth\Profile;
use App\Enums\ProfileTypesEnum;
use App\Models\Auth\ProfileType;

class ProfileTypeRepository
{
    public function updatePlateColors(ProfileType $profileType, array $data)
    {
        $profileType->plateColors()->sync(isset($data['plate_colors']) ? $data['plate_colors'] : []);

        return $profileType->load($profileType::relations());
    }

    public function getMembers()
    {
        $profiles = Profile::query()
            ->select(['id', 'number', 'type_id', 'institution_id', 'identity_id', 'suspended', 'suspended_at', 'created_at'])
            ->with(['institution:id,name,telephone,email,ifu', 'identity:id,firstname,lastname,email,telephone,npi,ifu', 'roles:id,label'])
            ->orderBy('created_at', 'asc')
            ->filter();

        if (getOnlineProfile()->type->code === ProfileTypesEnum::court->name) {
            $profiles->where([
                ['type_id', getOnlineProfile()->type_id],
                ['institution_id', getOnlineProfile()->institution_id]
            ]);
        }else{
            $profiles->where('type_id', getOnlineProfile()->type_id);
        }

        return $profiles->paginate(request('per_page', 20));
    }

    /**
     * @param array $data
     * @return Profile
     */
    public function toggleMemberStatus(array $data): Profile
    {
        $profile = Profile::find($data['profile_id']);

        $profile->update([
            'suspended' => !$profile->suspended,
            'suspended_at' => !$profile->suspended ? now() : null,
        ]);

        return $profile;
    }
}
