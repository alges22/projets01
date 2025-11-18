<?php

namespace App\Repositories\Space;

use App\Consts\NotificationNames;
use App\Enums\SpaceTemplateEnum;
use App\Enums\Status;
use App\Models\Space\Space;
use App\Models\Space\SpaceRegistrationRequest;
use App\Models\Auth\ProfileType;
use App\Models\Institution\Institution;
use App\Models\SimvebFile;
use App\Notifications\NotificationSender;
use App\Services\IdentityService;
use App\Services\InvitationService;
use App\Traits\UploadFile;
use App\Traits\UserDataTrait;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SpaceRegistrationRequestRepository
{
    use UploadFile, UserDataTrait;

    /**
     * @return array
     */
    public function getCreateData()
    {

        return [
            'profile_types' => ProfileType::query()->select(['id', 'code', 'name'])->get(),
            'required_document_types' => SpaceRegistrationRequest::requiredDocumentTypes(),
            'tamplates' => SpaceTemplateEnum::toNameValue(),
            'institutions' => Institution::query()->whereDoesntHave('space')->select(['id','name'])->get(),
        ];
    }

    public function store(array $data)
    {

        DB::beginTransaction();
        try {
            $documentsData = Arr::pull($data, 'documents');

            $data['creator_id'] = getOnlineProfile()?->id;

            $spaceRegistrationRequest = SpaceRegistrationRequest::create($data);

            if ($documentsData) {
                foreach ($documentsData as $document) {
                    $fileInfo = $this->saveFile($document['file'], "affilate_registration");

                    $spaceRegistrationRequest->files()->create([
                        'path' => $fileInfo,
                        'type' => SimvebFile::FILE,
                        'file_type_id' => $document['type_id'],
                    ]);
                }
            }

            DB::commit();

            return [true, $spaceRegistrationRequest->load(SpaceRegistrationRequest::relations())];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function validateRegistration(SpaceRegistrationRequest $spaceRegistrationRequest)
    {
        DB::beginTransaction();
        try {
            if ($spaceRegistrationRequest->status != Status::pending->name) {
                return [false, ['message' => 'Impossible de faire cette action sur cette demande.', 'code' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY]];
            }


            $space = Space::create([
                'request_id' => $spaceRegistrationRequest->id,
                'institution_id' => $spaceRegistrationRequest->institution_id,
                'profile_type_id' => $spaceRegistrationRequest->profile_type_id,
                'template' => $spaceRegistrationRequest->template,
            ]);

            (new InvitationService)->store([
                'npi' =>  $spaceRegistrationRequest->first_member_npi,
                'space_id' => $space->id,
                'profile_type_id' => $space->profile_type_id,
                'roles' => $space->profileType->roles()->pluck('id')->toArray()
            ]);

            // TODO: send sms to first member npi
            sendMail(
                (new IdentityService)->getIdentityByNpi($spaceRegistrationRequest->first_member_npi)->email,
                null,
                NotificationNames::SPACE_ADMIN_INVITATION,
                ['company_name' => $space->institution?->name]
            );

            $spaceRegistrationRequest->update([
                'status' => Status::validated->name,
                'validated_at' => now(),
                'validator_id' => getOnlineProfile()?->id,
            ]);

            DB::commit();
            return [true, $spaceRegistrationRequest->load($spaceRegistrationRequest::relations())];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function rejectRegistration(SpaceRegistrationRequest $spaceRegistrationRequest, array $data)
    {
        if ($spaceRegistrationRequest->status != Status::pending->name) {
            return [false, ['message' => 'Impossible de faire cette action sur cette demande.', 'code' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY]];
        }

        $spaceRegistrationRequest->update([
            'status' => Status::rejected->name,
            'rejected_at' => now(),
            'rejector_id' => getOnlineProfile()?->id,
            'reject_reason' => $data['reason'],
        ]);

        return [true, $spaceRegistrationRequest->load($spaceRegistrationRequest::relations())];
    }
}
