<?php

namespace App\Services\Treatment;

use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Exceptions\ImmatriculationFormatNotFoundException;
use App\Exceptions\NotificationConfigNotFoundException;
use App\Models\SaleDeclaration;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Order\Demand;
use App\Models\PlateTransformation;
use App\Models\PrestigeLabelImmatriculation;
use App\Models\Vehicle\Vehicle;
use App\Notifications\NotificationSender;
use App\Repositories\Immatriculation\GrayCardRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidateTreatmentService
{

    /**
     * @throws NotificationConfigNotFoundException
     */
    public function validateTreatment(array $data)
    {
        $demand = Demand::find($data['demand_id']);
        $treatment = $demand->activeTreatment;

        if ($treatment->pre_validated_at){
            $demand->getAdapter()->validate($demand);
            $message = "Demande validée avec succès";
        }else{
            $treatment->update([
                'pre_validated_by' => getOnlineProfile()?->id,
                'pre_validated_at' => now()
            ]);
            $demand->update(['status' => Status::pre_validated->name]);
            $message = "Demande pre-validée avec succès";
        }

        return [
            "demand" => $demand,
            "message" => $message,
            "success" => true
        ];
    }

    /* public function validateTreatmentByInterpol(array $data)
    {
        DB::beginTransaction();
        try {
            $treatment = $this->treatmentRepository->find($data['treatment_id']);
            $model = $treatment->model;

            if ($treatment->interpol_pre_validated_at != null){
                if (!$model->vehicle->category->immatriculationFormat){
                    throw new ImmatriculationFormatNotFoundException();
                }

                $treatment->update([
                    'interpol_validated_by' => Auth::id() ?? null,
                    'interpol_validated_at' => now()
                ]);
                $model->update(['status' => Status::validated_by_interpol->name]);

                //assign automatically to service
                if (serviceExist( $treatment->model->service->target_organization_id)){
                    $this->assignDemandToService([
                        'treatment_id' => $treatment->id,
                        'service_id' =>  $treatment->model->service->target_organization_id
                    ]);
                }

                switch ($model::class)
                {
                    case Demand::class :
                        if (Immatriculation::query()->where('vehicle_id', $model->vehicle->id)->doesntExist()){
                            $immatriculationNumber = $this->immatriculationNumberService->generateNewNumber($model->vehicle->category->immatriculationFormat,$model->vehicleOwner->town);
                            Immatriculation::query()->create([
                                'vehicle_id' => $model->vehicle->id,
                                'immatriculation_demand_id' => $model->id,
                                'number' => $immatriculationNumber,
                                'number_label' => strtoupper(implode(' ', $immatriculationNumber)),
                            ]);
                        }

                        break;
                }
            }else{
                $treatment->update([
                    'interpol_pre_validated_by' => Auth::id() ?? null,
                    'interpol_pre_validated_at' => now()
                ]);
                $model->update(['status' => Status::pre_validated_by_interpol->name]);
            }

            DB::commit();
            return $model->load($model::relations())->refresh();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

    } */
}
