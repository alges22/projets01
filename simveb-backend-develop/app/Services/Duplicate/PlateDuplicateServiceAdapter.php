<?php

namespace App\Services\Duplicate;

use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Order\Demand;
use App\Models\PlateDuplicate;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Traits\Demands\PrintOrderTrait;


class PlateDuplicateServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait, PrintOrderTrait;

    private TreatmentService $treatmentService;

    public function __construct()
    {
        $this->initRepository(PlateDuplicate::class);
        $this->treatmentService = new TreatmentService;
    }

    public function initDemand(Demand $demand, array $data): Model
    {
        $plateDuplicateData = Arr::only($data, [
            'old_plate_id',
        ]);

        if ($data['is_lost']) {
            $plateDuplicateData['reason'] = PlateDuplicate::IS_LOST;
        } else if ($data['is_spoiled']) {
            $plateDuplicateData['reason'] = PlateDuplicate::IS_SPOILED;
        }

        $plateDuplicateData += [
            'reference' => $this->generateUniqueReference(),
            'demand_id' => $demand->id,
        ];
        $plateDuplicate = $this->repository->storeOrUpdate($plateDuplicateData);
        $demand->update([
            'model_type' => $plateDuplicate::class,
            'model_id' => $plateDuplicate->id,
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
                    $treatment->update([
                        'validated_by' => getOnlineProfile()?->id,
                        'validated_at' => now()
                    ]);

                    $demand->plateDuplicate->oldPlate->update(['is_valid' => false]);

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
            $reference = generateReference('PD-');
        } while (PlateDuplicate::where('reference', $reference)->exists());

        return $reference;
    }
}
