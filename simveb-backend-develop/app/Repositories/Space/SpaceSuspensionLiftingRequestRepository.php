<?php

namespace App\Repositories\Space;

use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Models\Space\Space;
use App\Models\Space\SpaceSuspensionLiftingRequest;
use App\Notifications\NotificationSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SpaceSuspensionLiftingRequestRepository
{
    public function createData()
    {
        return [
            'spaces' => Space::where('status', Status::suspended->name)->with('institution:id,name', 'profileType:id,name')->select(['id', 'institution_id', 'profile_type_id', 'status'])->get(),
        ];
    }

    public function validateOrReject(SpaceSuspensionLiftingRequest $spaceSuspensionLiftingRequest, array $data)
    {
        DB::beginTransaction();
        try {
            $notifData = [
                'space_name' => $spaceSuspensionLiftingRequest->space->institution ? $spaceSuspensionLiftingRequest->space->institution->name : $spaceSuspensionLiftingRequest->space->type_label
            ];

            if ($data['action'] === 'validate') {
                $spaceSuspensionLiftingRequest->update([
                    'status' => Status::validated->name,
                    'validator_id' => getOnlineProfile()->id,
                    'validated_at' => now(),
                ]);

                $spaceSuspensionLiftingRequest->space->update(['status' => Status::active->name]);

                sendMail(
                    $spaceSuspensionLiftingRequest->author->identity->email,
                    null,
                    NotificationNames::SPACE_SUSPENSION_LIFTING_REQUEST_VALIDATED,                       
                    data: $notifData
                );
            } else {
                $spaceSuspensionLiftingRequest->update([
                    'status' => Status::rejected->name,
                    'rejector_id' => getOnlineProfile()->id,
                    'rejected_at' => now(),
                    'reject_reason' => $data['reject_reason'],
                ]);

                $notifData['reason'] = $data['reject_reason'];

                sendMail(
                    $spaceSuspensionLiftingRequest->author->identity->email,
                    null,
                    NotificationNames::SPACE_SUSPENSION_LIFTING_REQUEST_REJECTED,                       
                    data: $notifData
                );
            }

            DB::commit();

            return $spaceSuspensionLiftingRequest;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }
}
