<?php

namespace App\Repositories\Space;

use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Models\Space\Space;
use App\Models\Space\SpaceSuspensionRequest;
use App\Notifications\NotificationSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SpaceSuspensionRequestRepository
{
    public function createData()
    {
        return [
            'spaces' => Space::where('status', Status::active->name)->with('institution:id,name', 'profileType:id,name')->select(['id', 'institution_id', 'profile_type_id', 'status'])->get(),
        ];
    }

    public function validateOrReject(SpaceSuspensionRequest $spaceSuspensionRequest, array $data)
    {
        DB::beginTransaction();
        try {
            $notifData = [
                'space_name' => $spaceSuspensionRequest->space->institution ? $spaceSuspensionRequest->space->institution->name : $spaceSuspensionRequest->space->type_label
            ];

            if ($data['action'] === 'validate') {
                $spaceSuspensionRequest->update([
                    'status' => Status::validated->name,
                    'validator_id' => getOnlineProfile()->id,
                    'validated_at' => now(),
                ]);

                $spaceSuspensionRequest->space->update(['status' => Status::suspended->name]);

                sendMail(
                    $spaceSuspensionRequest->author->identity->email,
                    null,
                    NotificationNames::SPACE_SUSPENSION_REQUEST_VALIDATED,                       
                    data: $notifData
                );
            } else {
                $spaceSuspensionRequest->update([
                    'status' => Status::rejected->name,
                    'rejector_id' => getOnlineProfile()->id,
                    'rejected_at' => now(),
                    'reject_reason' => $data['reject_reason'],
                ]);

                $notifData['reason'] = $data['reject_reason'];

                sendMail(
                    $spaceSuspensionRequest->author->identity->email,
                    null,
                    NotificationNames::SPACE_SUSPENSION_REQUEST_REJECTED,                       
                    data: $notifData
                );
            }

            DB::commit();

            return $spaceSuspensionRequest;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }
}
