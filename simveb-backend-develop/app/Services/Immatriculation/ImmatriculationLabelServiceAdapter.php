<?php

namespace App\Services\Immatriculation;

use App\Consts\AvailableServiceType;
use App\Enums\DemandUpdatesTypeEnum;
use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Immatriculation\ImmatriculationLabel;
use App\Models\Order\Demand;

use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use App\Traits\Demands\PrintOrderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImmatriculationLabelServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait, PrintOrderTrait;

    private TreatmentService $treatmentService;
    private ImmatriculationNumberService $immatriculationNumberService;

    public function __construct()
    {
        $this->initRepository(ImmatriculationLabel::class);
        $this->treatmentService = new TreatmentService;
        $this->immatriculationNumberService = new ImmatriculationNumberService;
    }

    public function initDemand(Demand $demand, array $data): Model
    {
        $immData = Arr::only($data, [
            'plate_color_id',
            'front_plate_shape_id',
            'back_plate_shape_id',
            'label',
        ]);
        $immData += [
            'demand_id' => $demand->id,
            'immatriculation_id' => $demand->vehicle->immatriculation->id,
        ];
        $immatriculation = $this->repository->storeOrUpdate($immData);

        $demand->update([
            'model_type' => $immatriculation::class,
            'model_id' => $immatriculation->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {
            if ($treatment->pre_validated_at){
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);
                $demand->update(['status' => Status::validated->name]);
                $immatriculation = $demand->vehicle->immatriculation;
                $immatriculation->update(['label' => $demand->model->label]);
                //auto emit print order
                $this->emitPrintOrder($demand);

            }else{
                $treatment->update([
                    'pre_validated_by' => getOnlineProfile()?->id,
                    'pre_validated_at' => now()
                ]);
                $demand->update(['status' => Status::pre_validated->name]);
            }

            DB::commit();
            return $demand->refresh();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    public function submit(Demand $demand): Model|Demand
    {
        return $demand;
    }

    public function update(Demand $demand, $data)
    {
        $types = [
            'plate_color_id' => DemandUpdatesTypeEnum::plate_color->name,
            'front_plate_shape_id' => DemandUpdatesTypeEnum::front_plate_shape->name,
            'back_plate_shape_id' => DemandUpdatesTypeEnum::back_plate_shape->name,
            'label' => DemandUpdatesTypeEnum::label->name,
        ];

        $immData = Arr::only($data, [
            'plate_color_id',
            'front_plate_shape_id',
            'back_plate_shape_id',
            'label',
        ]);

        $immData = array_filter($immData, fn($value) => !is_null($value) && $value !== '');

        $changes = [];
        foreach ($immData as $key => $value) {
            $old_value = $demand->immatriculation_label[$key];
            if(is_null($old_value)) {
                $old_value = $demand->immatriculation_label->immatriculation[$key];
            }
            if($value != $old_value){
                $changes[$key] = $value;
                saveDemandUpdateHistory([
                    'old_value' => $old_value,
                    'new_value' => $value,
                    'type' => $types[$key],
                    'demand_id' => $demand->id,
                    'model_type' => ImmatriculationLabel::class,
                    'model_id' => $demand->immatriculation_label->id,
                ]);
            }
        }

        $old_immatriculation_label = $demand->immatriculation_label;
        $immatriculation_label = $this->repository->update($old_immatriculation_label, $changes);
        return $immatriculation_label;
    }
}
