<?php

namespace App\Services\Reimmatriculation;

use App\Enums\DemandUpdatesTypeEnum;
use App\Enums\Status;
use App\Exceptions\ImmatriculationFormatNotFoundException;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Immatriculation\ImmatriculationLabel;
use App\Models\Order\Demand;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\PlateTransformation;
use App\Models\Reimmatriculation;
use App\Repositories\Crud\CrudRepository;
use App\Services\Immatriculation\ImmatriculationNumberService;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use App\Traits\Demands\PrintOrderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ReimmatriculationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait, PrintOrderTrait;

    private TreatmentService $treatmentService;
    private ImmatriculationNumberService $immatriculationNumberService;
    private CrudRepository $immatriculationRepo;
    private CrudRepository $immatriculationLabelRepo;
    private CrudRepository $plateTransformationRepo;

    public function __construct()
    {
        $this->initRepository(Reimmatriculation::class);
        $this->treatmentService = new TreatmentService;
        $this->immatriculationRepo = new CrudRepository(Immatriculation::class);
        $this->immatriculationLabelRepo = new CrudRepository(ImmatriculationLabel::class);
        $this->plateTransformationRepo = new CrudRepository(PlateTransformation::class);
        $this->immatriculationNumberService = new ImmatriculationNumberService;
    }

    public function initDemand(Demand $demand, array $data): Model|Demand
    {
        $reimmatriculationData = Arr::only($data, [
            'reason_id',
            'custom_reason',
            'vehicle_id',
            'vehicle_owner_id',
            'with_immatriculation',
            'with_plate_transformation',
            'vehicle_owner_id',
            'plate_color_id',
            'back_plate_shape_id',
            'front_plate_shape_id',
            'label',
            'desired_number',
        ]);

        $reimmatriculationData += [
            'demand_id' => $demand->id,
        ];

        $reimmatriculation = $this->repository->storeOrUpdate($reimmatriculationData, ['vehicle_id' => $data['vehicle_id']]);

        $demand->update([
            'model_type' => $reimmatriculation::class,
            'model_id' => $reimmatriculation->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model|Demand
    {
        $treatment = $demand->activeTreatment;
        $reimmatriculation = $demand->model;

        DB::beginTransaction();
        try {
            if (!$treatment->pre_validated_at) {
                $treatment->update([
                    'pre_validated_by' => getOnlineProfile()?->id,
                    'pre_validated_at' => now()
                ]);

                $demand->update(['status' => Status::pre_validated->name]);
            } else {
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);

                if ($reimmatriculation->with_immatriculation) {

                    $immatriculation = $this->immatriculationRepo->store([
                        'vehicle_id' => $reimmatriculation->vehicle_id,
                        'vehicle_owner_id' => $reimmatriculation->vehicle_owner_id,
                        'plate_color_id' => $reimmatriculation->plate_color_id,
                        'front_plate_shape_id' => $reimmatriculation->front_plate_shape_id,
                        'back_plate_shape_id' => $reimmatriculation->back_plate_shape_id,
                        'status' => Status::validated->name,
                        'label' => $reimmatriculation->label ?? null,
                        'desired_number' => $reimmatriculation->desired_number ?? null,
                    ]);

                    $reimmatriculation->update([
                        'status' => Status::validated->name,
                        'immatriculation_id' => $immatriculation->id,
                    ]);

                    if ($immatriculation->label) {
                        $immatriculationLabel = $this->immatriculationLabelRepo->store([
                            'immatriculation_id' => $immatriculation->id,
                            'status' => Status::validated->name,
                            'label' => $immatriculation->label,
                            'plate_color_id' => $immatriculation->plate_color_id,
                            'front_plate_shape_id' => $immatriculation->front_plate_shape_id,
                            'back_plate_shape_id' => $immatriculation->back_plate_shape_id,
                        ]);
                    }

                    $this->generateImmatriculationNumber($immatriculation, $immatriculation->desired_number);
                } else {
                    $plateTransformation = $this->plateTransformationRepo->store([
                        'status' => Status::validated->name,
                        'vehicle_id' => $reimmatriculation->vehicle_id,
                        'plate_color_id' => $reimmatriculation->plate_color_id,
                        'front_plate_shape_id' => $reimmatriculation->front_plate_shape_id,
                        'back_plate_shape_id' => $reimmatriculation->back_plate_shape_id,
                    ]);

                    $immatriculation = $reimmatriculation->vehicle->immatriculations()->whereHas('demand', function ($q) {
                        $q->where('demands.status', Status::closed->name)
                            ->orWhere('demands.status', Status::print_order_validated->name)
                            ->orWhere('demands.status', Status::print_order_emitted->name)
                            ->orWhere('demands.status', Status::validated->name);
                    })->latest()->first();

                    $reimmatriculation->update([
                        'status' => Status::validated->name,
                        'immatriculation_id' => $immatriculation->id,
                        'plate_transformation_id' => $plateTransformation->id,
                    ]);

                    $reimmatriculation->vehicle->immatriculation->update([
                        'plate_color_id' => $reimmatriculation->plate_color_id,
                        'front_plate_shape_id' => $reimmatriculation->front_plate_shape_id,
                        'back_plate_shape_id' => $reimmatriculation->back_plate_shape_id,
                    ]);
                }

                $demand->update(['status' => Status::validated->name]);

                $this->emitPrintOrder($demand);
            }

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
        return $demand;
    }

    /**
     * @throws ImmatriculationFormatNotFoundException
     */
    private function generateImmatriculationNumber(Immatriculation $immatriculation, ?string $desiredNumber): void
    {
        $vehicle = $immatriculation->vehicle;
        $vehicleOwner = $immatriculation->vehicleOwner;

        if (!$immatriculationFormat = $vehicleOwner->profile->type->immatriculationFormat) {
            $immatriculationFormat = $vehicle->category->immatriculationFormat;
        }
        if (!$immatriculationFormat) {
            throw new ImmatriculationFormatNotFoundException();
        }

        $immatriculationNumber = $this->immatriculationNumberService->generateNewNumber($immatriculationFormat, $vehicleOwner->identity->town, $desiredNumber);
        $immatriculation->update([
            'immatriculation_format_id' => $immatriculationFormat->id,
            'number' => $immatriculationNumber['format'],
            'issued_at' => now(),
            'number_label' => strtoupper(implode(' ', $immatriculationNumber['format'])),
            'prefix' => $immatriculationNumber['components']['prefix'],
            'alphabetic_label' => $immatriculationNumber['components']['alphabetic_label'],
            'zone' => $immatriculationNumber['components']['zone'],
            'numeric_label' => $immatriculationNumber['components']['numeric_label'],
            'country_code' => $immatriculationNumber['components']['country_code'],
        ]);
    }


    public function update($demand, $data)
    {
        $reimmatriculation = $demand->model;
        foreach ($data as $key => $val) {
            if ($val != $reimmatriculation->$key) {
                if ($key == 'plate_color_id') {
                    $oldVal = $reimmatriculation->plateColor->name;
                    $val = PlateColor::find($val)->name;
                } else if ($key == 'front_plate_shape_id') {
                    $oldVal = $reimmatriculation->frontPlateShape->name;
                    $val = PlateShape::find($val)->name;
                } else if ($key == 'back_plate_shape_id') {
                    $oldVal = $reimmatriculation->backPlateShape->name;
                    $val = PlateShape::find($val)->name;
                } else {
                    $oldVal = $reimmatriculation->$key;
                    $val = $val;
                }

                $key = str_replace('_id', '', $key);

                saveDemandUpdateHistory([
                    'old_value' => $oldVal,
                    'new_value' => $val,
                    'type' => DemandUpdatesTypeEnum::toNameValue()[$key],
                    'demand_id' => $demand->id,
                ]);
            }
        }

        $reimmatriculation = $this->repository->update($reimmatriculation, $data);

        if ($reimmatriculation->with_immatriculation) {
            $reimmatriculation->immatriculation->update([
                'plate_color_id' => $reimmatriculation->plate_color_id,
                'front_plate_shape_id' => $reimmatriculation->front_plate_shape_id,
                'back_plate_shape_id' => $reimmatriculation->back_plate_shape_id,
                'label' => $reimmatriculation->label,
                'desired_number' => $reimmatriculation->desired_number,
            ]);
        } else {
            $reimmatriculation->plateTransformation->update([
                'plate_color_id' => $reimmatriculation->plate_color_id,
                'front_plate_shape_id' => $reimmatriculation->front_plate_shape_id,
                'back_plate_shape_id' => $reimmatriculation->back_plate_shape_id,
            ]);

            $reimmatriculation->vehicle->immatriculation->update([
                'plate_color_id' => $reimmatriculation->plate_color_id,
                'front_plate_shape_id' => $reimmatriculation->front_plate_shape_id,
                'back_plate_shape_id' => $reimmatriculation->back_plate_shape_id,
            ]);
        }
    }
}
