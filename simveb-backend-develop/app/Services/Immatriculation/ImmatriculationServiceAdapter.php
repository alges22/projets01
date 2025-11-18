<?php

namespace App\Services\Immatriculation;

use App\Consts\AvailableServiceType;
use App\Enums\DemandUpdatesTypeEnum;
use App\Enums\LegalStatusEnum;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Exceptions\ImmatriculationFormatNotFoundException;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Auth\ProfileType;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Immatriculation\ImmatriculationLabel;
use App\Models\Order\Demand;

use App\Repositories\Crud\CrudRepository;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use App\Traits\Demands\PrintOrderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Identity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImmatriculationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait, PrintOrderTrait;

    private TreatmentService $treatmentService;
    private ImmatriculationNumberService $immatriculationNumberService;
    private CrudRepository $immatriculationLabelRepo;

    public function __construct()
    {
        $this->initRepository(Immatriculation::class);
        $this->immatriculationLabelRepo = new CrudRepository(ImmatriculationLabel::class);
        $this->treatmentService = new TreatmentService;
        $this->immatriculationNumberService = new ImmatriculationNumberService;
    }

    public function initDemand(Demand $demand, array $data): Model
    {
        $immData = Arr::only($data, [
            'plate_color_id',
            'front_plate_shape_id',
            'back_plate_shape_id',
        ]);

        if ($demand->service->type->code == AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL){
            $immData = [...$immData, ...Arr::only($data, [
                'desired_number',
                'label',
            ])];
        }

        if (isset($data['desired_number']) && $demand->service->type->code == AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER){
            $immData['desired_number'] = $data['desired_number'];
        }

        $immData = [...$immData, ...[
            'demand_id' => $demand->id,
            'vehicle_id' => $demand->vehicle->id,
            'vehicle_owner_id' => $demand->vehicle_owner_id,
        ]];
        $immatriculation = $this->repository->storeOrUpdate($immData, ['vehicle_id' => $immData['vehicle_id']]);
        if (isset($data['label'])){
            unset($immData['desired_number'], $immData['vehicle_id'], $immData['vehicle_owner_id']);
            $immData['immatriculation_id'] = $immatriculation->id;
            $this->immatriculationLabelRepo->storeOrUpdate($immData, ['immatriculation_id' => $immatriculation->id]);
        }

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
                $demand->update(['status' => Status::validated->name]);
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);
                $demand->model->update(['status' => Status::validated->name]);
                if ($demand->model->label){
                    $this->immatriculationLabelRepo->model
                        ->newQuery()
                        ->where('demand_id',$demand->id)
                        ->update(['status' => Status::validated->name]);
                }
                $this->generateImmatriculationNumber($demand, $demand->model->desired_number);
                //emit print order
                if(shouldAutoProcessStep($demand, Status::print_order_emitted->name)){
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


    public function update(Demand $demand, $data)
    {
        $types = [
            'plate_color_id' => DemandUpdatesTypeEnum::plate_color->name,
            'front_plate_shape_id' => DemandUpdatesTypeEnum::front_plate_shape->name,
            'back_plate_shape_id' => DemandUpdatesTypeEnum::back_plate_shape->name,
            'desired_number' => DemandUpdatesTypeEnum::desired_number->name,
            'label' => DemandUpdatesTypeEnum::label->name,
        ];

        $immData = Arr::only($data, [
            'plate_color_id',
            'front_plate_shape_id',
            'back_plate_shape_id',
        ]);

        if ($demand->service->type->code == AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL){
            $immData = [...$immData, ...Arr::only($data, [
                'desired_number',
                'label',
            ])];
        }

        if (isset($data['desired_number']) && $demand->service->type->code == AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER){
            $immData['desired_number'] = $data['desired_number'];
        }

        if (isset($data['label'])){
            unset($immData['desired_number']);
        }

        $immData = array_filter($immData, fn($value) => !is_null($value) && $value !== '');

        $changes = [];
        foreach ($immData as $key => $value) {
            if($value != $demand->immatriculation[$key]){
                $changes[$key] = $value;
                saveDemandUpdateHistory([
                    'old_value' => $demand->immatriculation[$key],
                    'new_value' => $value,
                    'type' => $types[$key],
                    'demand_id' => $demand->id,
                    'model_type' => Immatriculation::class,
                    'model_id' => $demand->immatriculation->id,
                ]);
            }
        }

        $old_immatriculation = $demand->immatriculation;
        $immatriculation = $this->repository->update($old_immatriculation, $changes);

        return $immatriculation;
    }

    /**
     * @throws ImmatriculationFormatNotFoundException
     */
    private function generateImmatriculationNumber(Demand $demand, ?string $desiredNumber): void
    {
        $vehicle = $demand->vehicle;
        $vehicleOwner = $demand->vehicleOwner;
        $profileType = $vehicleOwner->legal_status === LegalStatusEnum::moral->name ? ProfileType::where('code', ProfileTypesEnum::company->name)->first() : $vehicleOwner->profile->type;

        if (!$immatriculationFormat = $profileType->immatriculationFormat()->where('vehicle_category_id', $vehicle->category->id)->first()){
            $immatriculationFormat = $vehicle->category->immatriculationFormat;
        }
        if (!$immatriculationFormat){
            throw new ImmatriculationFormatNotFoundException;
        }

        $town = $vehicleOwner->legal_status === LegalStatusEnum::moral->name ? Identity::where('npi', $vehicleOwner->institution->space->registrationRequest->first_member_npi)->first()->town : $vehicleOwner->identity->town;

        $immatriculationNumber = $this->immatriculationNumberService->generateNewNumber($immatriculationFormat, $town, $desiredNumber);
        $immatriculation = $demand->model;
        $immatriculation->update([
            'vehicle_id' => $vehicle->id,
            'immatriculation_demand_id' => $demand->id,
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
}
