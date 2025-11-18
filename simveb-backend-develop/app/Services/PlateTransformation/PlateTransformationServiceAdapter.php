<?php
namespace App\Services\PlateTransformation;

use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Order\Demand;
use App\Models\PlateTransformation;
use App\Services\Treatment\TreatmentService;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class PlateTransformationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    private TreatmentService $treatmentService;

    public function __construct()
    {
        $this->initRepository(PlateTransformation::class);
    }

    public function initDemand(Demand $demand, array $data): Model|Demand
    {
        $transformationData = Arr::only($data, [
            'plate_color_id', 'front_plate_shape_id', 'back_plate_shape_id',
        ]);

        $transformationData += [
            'demand_id' => $demand->id,
            'vehicle_id' => $demand->vehicle->id,
        ];

        $transformation = $this->repository->storeOrUpdate($transformationData);

        $demand->update([
            'model_type' => $transformation::class,
            'model_id' => $transformation->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model|Demand
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {
            $treatment->update([
                'validated_by' => getOnlineProfile()?->id,
                'validated_at' => now()
            ]);

            $demand->model->update(['status' => Status::validated->name]);
            $demand->update(['status' => Status::validated->name]);

            $immatriculation = $demand->vehicle->immatriculation;
            // TODO: re start plate immatriculation order with the same immatriculation number

            DB::commit();
            return $demand->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function submit(Demand $demand): Model|Demand
    {
        return $this->treatmentService->submitDemand($demand);
    }

}
