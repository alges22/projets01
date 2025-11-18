<?php

namespace App\Repositories\Demand;

use App\Enums\DemandUpdatesTypeEnum;
use App\Models\DemandUpdatesHistory;
use App\Repositories\Crud\CrudRepositoryHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DemandUpdatesHistoryRepository extends CrudRepositoryHandler
{
    public function __construct()
    {
        parent::__construct(DemandUpdatesHistory::class);
    }


    public function isHistoryValidated($demandId)
    {
        $types = $this->model->newQuery()
            ->where('demand_id', $demandId)
            ->pluck('type')
            ->unique()
            ->toArray();

        $historyIsValidated = true;
        foreach ($types as $type) {
            $histories = $this->model->newQuery()
                ->where('demand_id', $demandId)
                ->where('type', $type);

            if ($type === DemandUpdatesTypeEnum::attachments->name) {
                $attachmentUpdateHistories = $histories->get();
                foreach ($attachmentUpdateHistories->unique('model_id')->pluck('model_id') as $attachmentTypeId)
                {
                    $documentHistory = $attachmentUpdateHistories->toQuery()->where('model_id', $attachmentTypeId)->latest()->first();
                    if (!$documentHistory->is_validated) {
                        $historyIsValidated = false;
                        break;
                    }
                }
            } else {
                $history = $histories->latest()->first();
                if (!$history->is_validated) {
                    $historyIsValidated = false;
                    break;
                }
            }
        }

        return $historyIsValidated;
    }

    public function validate(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['demand_updates'] as $id) {
                $updateHistory = parent::find($id);
                parent::update($updateHistory, [
                    'is_validated' => true,
                ]);
            }
            DB::commit();
            return [
                'message' => "Mise(s) à jour validée avec succès.",
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
