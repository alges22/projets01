<?php

namespace App\Services\GlassEngraving;

use App\Enums\Status;
use App\Models\GlassEngraving;
use Illuminate\Support\Arr;
use App\Models\Order\Demand;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\DemandServiceAdapter;
use App\Models\TintedWindowAuthorization;
use App\Services\Treatment\TreatmentService;

class GlassEngravingServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    public function __construct(private TreatmentService $treatmentService)
    {
        $this->initRepository(GlassEngraving::class);
    }

    /**
     * @param Demand $demand
     * @param array $data
     * @return Model|Demand
     */
    public function initDemand(Demand $demand, array $data): Model|Demand
    {
        $engravingData = Arr::only($data, [
            'vehicle_id',
            'vehicle_owner_id',
        ]);
        $engravingData += [
            'demand_id' => $demand->id,
            'status' => Status::pending->name,
        ];
        $glassEngraving = $this->repository->storeOrUpdate($engravingData);
        $demand->update([
            'model_type' => $glassEngraving::class,
            'model_id' => $glassEngraving->id,
        ]);

        return $demand;
    }

    /**
     * @param Demand $demand
     * @return Model|Demand
     * @throws \Throwable
     */
    public function validate(Demand $demand): Model|Demand
    {
        $treatment = $demand->activeTreatment;

        DB::beginTransaction();
        try {
            if ($treatment->pre_validated_at) {

                $demand->model->createCertificate('certificates.glass-engraving');

                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);
                $demand->model->update(['status' => Status::validated->name]);
                $demand->update(['status' => Status::validated->name]);
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
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    /**
     * @param Demand $demand
     * @return Model|Demand
     */
    public function submit(Demand $demand): Model|Demand
    {
        return $this->treatmentService->submitDemand($demand);
    }
}
