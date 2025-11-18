<?php

namespace App\Repositories\Space;

use App\Models\Space\Space;
use App\Models\Space\SpaceRegistrationRequest;
use App\Models\Auth\Profile;
use App\Models\SimvebFile;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Position;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SpaceRepository
{
    use UploadFile;

    public function edit()
    {
        return [
            'positions' => Position::get(['id', 'name', 'description']),
            'required_document_types' => SpaceRegistrationRequest::requiredDocumentTypes(),
        ];
    }

    /**
     * @param Space $space
     * @param array $data
     * @return Space $spaceType
     */
    public function update(Space $space, array $data)
    {
        DB::beginTransaction();
        try {
            $space->update($data);

            DB::commit();
            return $space->load(Space::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function members()
    {
        $onlineProfile = auth()->user()->onlineProfile;

        if ($onlineProfile->space) {
            return $onlineProfile->space->profiles()->with(['user.identity', 'roles'])->get();
        } else {
            return Profile::where('type_id', $onlineProfile->type_id)->with(['user.identity'])->get();
        }
    }

    /**
     *  @return Model
     */
    public function details()
    {
        $onlineProfile = getOnlineProfile();

        if ($onlineProfile->institution_id)
        {
            $spaceProfiles = $onlineProfile->space->profiles()->pluck('id')->toArray();
            return [
                'space_infos' => $onlineProfile->space->load(['institution:id,name']),
                'members' => [
                    'total' => $onlineProfile->space->profiles()->count(),
                    'list' => $this->members(),
                ],
                'activities' => Activity::whereIn('causer_id', $spaceProfiles)->latest()->get(),
            ];
        }
    }
}
