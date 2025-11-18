<?php

namespace App\Services\Duplicate;

use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\GrayCardDuplicate;
use App\Models\Order\Demand;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Traits\Demands\PrintOrderTrait;


class GrayCardDuplicateServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait, PrintOrderTrait;

    private TreatmentService $treatmentService;

    public function __construct()
    {
        $this->initRepository(GrayCardDuplicate::class);
        $this->treatmentService = new TreatmentService;
    }

    public function initDemand(Demand $demand, array $data): Model
    {
        $grayCardDuplicateData = [
            'reference' => $this->generateUniqueReference(),
            'demand_id' => $demand->id,
            'old_gray_card_id' => $demand->vehicle->grayCard->id
        ];

        if ($data['is_lost']) {
            $grayCardDuplicateData['reason'] = GrayCardDuplicate::IS_LOST;
        } else if ($data['is_spoiled']) {
            $grayCardDuplicateData['reason'] = GrayCardDuplicate::IS_SPOILED;
        }
        $grayCardDuplicate = $this->repository->storeOrUpdate($grayCardDuplicateData);
        $demand->update([
            'model_type' => $grayCardDuplicate::class,
            'model_id' => $grayCardDuplicate->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {

            if ($treatment->pre_validated_at) {
                if ($demand->status != Status::validated->name) {

                    $demand->grayCardDuplicate->oldGrayCard->update(['is_active' => false]);

                    $treatment->update([
                        'validated_by' => getOnlineProfile()?->id,
                        'validated_at' => now()
                    ]);

                    $demand->update(['status' => Status::validated->name]);

                    $this->emitPrintOrder($demand);
                }
            } else {
                $treatment->update([
                    'pre_validated_by' => getOnlineProfile()?->id,
                    'pre_validated_at' => now()
                ]);
                $demand->update(['status' => Status::pre_validated->name]);
            }

            DB::commit();
            return $demand->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function submit(Demand $demand): Model|Demand
    {
        return $this->treatmentService->submitDemand($demand);
    }

    public function generateUniqueReference()
    {
        do {
            $reference = generateReference('GCD-');
        } while (GrayCardDuplicate::where('reference', $reference)->exists());

        return $reference;
    }
}
