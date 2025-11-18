<?php

namespace App\Services\Declaration;

use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Order\Demand;
use App\Models\SaleDeclaration;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use App\Services\VehicleOwnerService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SaleDeclarationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    private TreatmentService $treatmentService;
    private VehicleOwnerService $ownerService;
    public function __construct()
    {
        $this->initRepository(SaleDeclaration::class);
        $this->treatmentService = new TreatmentService;
        $this->ownerService = new VehicleOwnerService;
    }

    public function initDemand(Demand $demand, array $data): Model
    {
        $saleData = Arr::only($data, [
            'comment',
            'new_owner_npi',
            'new_owner_ifu',
            'vehicle_owner_id',
            'vehicle_id',
        ]);
        $saleData += [
            'reference' => $this->generateUniqueReference(),
            'demand_id' => $demand->id,
        ];
        $saleDeclaration = $this->repository->storeOrUpdate($saleData);
        $demand->update([
            'model_type' => $saleDeclaration::class,
            'model_id' => $saleDeclaration->id,
        ]);

        if(!$demand->otp?->buyer_otp && !$demand->otp?->is_verified) {
            $demand->update(['otp_verified' => false]);
        }

        return $demand;
    }

    public function validate(Demand $demand): Model
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try
        {

            if ($treatment->pre_validated_at){

                $demand->model->createCertificate();
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);

                $demand->update(['status' => Status::validated->name]);

                sendMail(
                    null,
                    $demand->vehicleOwner->identity,
                    NotificationNames::SALE_DECLARATION_VALIDATED,
                    ['attachment' => $demand->generateCertificate,'reference' => $demand->reference]
                );
                sendMail(
                    null,
                    $demand->model->buyer_notifiable,
                    NotificationNames::SALE_DECLARATION_VALIDATED,
                    ['attachment' => $demand->generateCertificate,'reference' => $demand->reference]
                );

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
        return $this->treatmentService->submitDemand($demand);
    }

    public function generateUniqueReference()
    {
        do{
            $reference = generateReference('DV');
        }while(SaleDeclaration::where('reference', $reference)->exists());

        return $reference;
    }
}
