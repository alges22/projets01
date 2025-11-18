<?php

namespace App\Services;

use App\Consts\Roles;
use App\Models\Auth\Profile;
use App\Enums\ProfileTypesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OppositionService
{

    public function getProfileInSameCourt($institutionId, $role)
    {
        $profile = Profile::query()
            ->where('suspended', false)
            ->whereHas('type', fn($query) =>
                $query->where('code', ProfileTypesEnum::court->name)
            )
            ->where('institution_id', $institutionId)
            ->whereHas('roles', fn($query) =>
                $query->where('name', $role)
            )->inRandomOrder()
        ->first();

        return $profile;
    }


    public function getClerkProfileInSameCourt($institutionId)
    {
        return $this->getProfileInSameCourt($institutionId, Roles::CLERK);
    }

    public function getJudgeProfileInSameCourt($institutionId)
    {
        return $this->getProfileInSameCourt($institutionId, Roles::INVESTIGATING_JUDGE);
    }


    public function getAnattProfileToAffectedPledge()
    {
        $anatt = Profile::query()
            ->where('suspended', false)
            ->whereHas('roles', fn($query) =>
                $query->where('name', Roles::ADMIN)
            )->get();

        return $anatt;
    }
}
