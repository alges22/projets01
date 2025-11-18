<?php

namespace App\Services\VehicleTransformation;

use App\Consts\AvailableServiceType;
use App\Enums\DemandUpdatesTypeEnum;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Config\Service;
use App\Models\DemandUpdatesHistory;
use App\Models\Order\Demand;
use App\Enums\Status;
use App\Enums\CharacteristicCategoriesEnum;
use App\Models\TransformationCharacteristic;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\VehicleTransformation;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use App\Traits\Demands\PrintOrderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VehicleTransformationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait, PrintOrderTrait;

    private TreatmentService $treatmentService;

    public function __construct()
    {
        $this->initRepository(VehicleTransformation::class);
        $this->treatmentService = new TreatmentService;
    }


    public function transformationCharacteristic($data)
    {
        $createdData = [];
        $vehicleId = VehicleTransformation::where('id', $data['transformation_id'])->pluck('vehicle_id')->first();
        $vehicle = Vehicle::with(['characteristics.category.types'])
            ->findOrFail($vehicleId);

        $vehicleCharacteristics = VehicleCharacteristic::with('category.types')
            ->whereIn('id', $data['value_id'])
            ->get();

        foreach ($vehicleCharacteristics as $key => $vehicleCharacteristic) {
            $oldCharacteristic = $vehicle->characteristics->firstWhere('category_id', $vehicleCharacteristic->category_id);
            $getData = [
                'transformation_id' => $data['transformation_id'],
                'old_characteristic' => $oldCharacteristic->id,
                'new_characteristic' => $vehicleCharacteristic->id,
            ];

            $transCharacteristic = TransformationCharacteristic::query()->where([
                ['transformation_id', $data['transformation_id']],
                ['new_characteristic', $vehicleCharacteristic->id],
            ])->first();

            if (!$transCharacteristic){
                $createdData[] = TransformationCharacteristic::create($getData);
            }
        }

        return ["created_transformations" => $createdData];
    }


    public function initDemand(Demand $demand, array $data): Model
    {
        $transformationData = [
            'demand_id' => $demand->id,
            'vehicle_id' => $demand->vehicle->id,
            'owner_id' => $demand->vehicle_owner_id,
        ];

        $transformation = $this->repository->storeOrUpdate($transformationData);

        $transformationData = [
            ...$transformationData,
            'transformation_id' => $transformation->id,
            'value_id' => $data['value_id'],
        ];

        $this->transformationCharacteristic($transformationData);

        $demand->update([
            'model_type' => $transformation::class,
            'model_id' => $transformation->id,
        ]);

        return $demand;
    }


    public function updateVehicleCharacteristic(Demand $demand): Model
    {
        $demandUpdateHistories = $demand->demandUpdatesHistories;
        if ($demandUpdateHistories) {
            DemandUpdatesHistory::whereIn('id', $demandUpdateHistories->pluck('id'))->update(['is_validated' => true]);
        }

        $vehicleTransformationService = Service::query()
            ->where([
                ['id', $demand->service_id],
                ['code', AvailableServiceType::VEHICLE_TRANSFORMATION]])
            ->first();

        if ($vehicleTransformationService) {
            $transformation = $demand->transformation;
            $vehicle = Vehicle::with(['characteristics.category.types'])->findOrFail($transformation->vehicle_id);
            $characteristics = TransformationCharacteristic::select(['old_characteristic', 'new_characteristic'])
                ->where('transformation_id', $transformation->id)
                ->get();
            $oldCharacteristics = $characteristics->pluck('old_characteristic')->filter()->toArray();
            $newCharacteristics = $characteristics->pluck('new_characteristic')->filter()->toArray();

            if (!empty($oldCharacteristics)) {
                $vehicle->characteristics()->detach($oldCharacteristics);
            }
            $vehicle->characteristics()->attach($newCharacteristics);
        }

        return $vehicle;
    }


    public function validate(Demand $demand): Model
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {
            if ($treatment->pre_validated_at){
                $demand->update(['status' => Status::validated->name]);
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now(),
                ]);

                $demand->transformation->update(['status' => Status::validated->name]);

                $this->updateVehicleCharacteristic($demand);

                $categories = [];
                $greyCardData =
                    [
                        CharacteristicCategoriesEnum::vehicle_energy->name,
                        CharacteristicCategoriesEnum::number_of_seats->name,
                        CharacteristicCategoriesEnum::empty_weight->name,
                        CharacteristicCategoriesEnum::charged_weight->name,
                        CharacteristicCategoriesEnum::engine_power->name,
                        CharacteristicCategoriesEnum::bodyshop->name,
                    ];
                $transformationCharacteristics = $demand->transformation->transformationCharacteristics;
                foreach ($transformationCharacteristics as $transformationCharacteristic) {
                    $category = $transformationCharacteristic->newCharacteristic->category->code;
                    $categories[] = $category;
                }
                if (in_array($categories, $greyCardData)) {
                    $demand->transformation->update(['grey_card' => true]);
                    $this->emitPrintOrder($demand);
                }

            }else{
                $demand->update(['status' => Status::pre_validated->name]);
                $treatment->update([
                    'pre_validated_by' => getOnlineProfile()?->id,
                    'pre_validated_at' => now()
                ]);
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


    public function update(Demand $demand, $data): Model
    {
        $types = [
            'new_characteristic' => DemandUpdatesTypeEnum::vehicle_characteristic->name,
        ];

        $transformationData = [
            'new_characteristic' => $data['value_id'],
        ];

        $changes = [];
        $transformationCharId = $data['transformation_characteristic_id'];
        $transformationChar = TransformationCharacteristic::firstWhere('id', $transformationCharId);

        foreach ($transformationData as $key => $value) {
            if ($transformationChar && $value != $transformationChar[$key]) {
                $changes[$key] = $value;
                saveDemandUpdateHistory([
                    'old_value' => $transformationChar[$key],
                    'new_value' => $value,
                    'type' => $types[$key],
                    'demand_id' => $demand->id,
                    'model_type' => VehicleTransformation::class,
                    'model_id' => $transformationChar->id,
                ]);
            }
        }

        return $this->repository->update($transformationChar, $changes);
    }


    public function delete($identity)
    {
        DB::beginTransaction();
        try {
            $transformationCharacteristic = TransformationCharacteristic::findOrFail($identity);
            $demandStatus = $transformationCharacteristic->transformation->demand->status;

            if (in_array($demandStatus, [Status::validated->name, Status::print_order_emitted->name, Status::closed->name], true)) {
                throw new HttpException(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible, d'effectuer cette action");
            }

            $transformationCharacterisitic = $this->repository->destroy($transformationCharacteristic);

            DB::commit();
            return $transformationCharacterisitic;
        } catch (\Exception $e) {
            DB::rollBack();
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

}
