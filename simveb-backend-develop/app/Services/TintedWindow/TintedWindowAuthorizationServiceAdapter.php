<?php

namespace App\Services\TintedWindow;

use App\Enums\Status;
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

class TintedWindowAuthorizationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    public function __construct(private TreatmentService $treatmentService)
    {
        $this->initRepository(TintedWindowAuthorization::class);
    }

    /**
     *
     */
    public function initDemand(Demand $demand, array $data): Model|Demand
    {
        $authorizationData = Arr::only($data, [
            'vehicle_id',
            'vehicle_owner_id',
        ]);
        $authorizationData += [
            'demand_id' => $demand->id,
            'status' => Status::pending->name,
        ];
        $tintedWindowsAuthorization = $this->repository->storeOrUpdate($authorizationData);
        $demand->update([
            'model_type' => $tintedWindowsAuthorization::class,
            'model_id' => $tintedWindowsAuthorization->id,
        ]);

        return $demand;
    }

    /**
     * @return Model|Demand
     */
    public function validate(Demand $demand): Model|Demand
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {
            if ($treatment->pre_validated_at) {

                $demand->model->createCertificate('certificates.tinted-window-authorization');

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
     *
     */
    public function submit(Demand $demand): Model|Demand
    {
        return $this->treatmentService->submitDemand($demand);
    }
}
