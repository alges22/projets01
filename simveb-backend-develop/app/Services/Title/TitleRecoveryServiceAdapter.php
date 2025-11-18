<?php

namespace App\Services\Title;

use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Order\Demand;
use App\Models\Title\TitleRecovery;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TitleRecoveryServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    private TreatmentService $treatmentService;


    public function __construct()
    {
        $this->initRepository(TitleRecovery::class);
        $this->treatmentService = new TreatmentService;
    }

    public function initDemand(Demand $demand, array $data): Model|Demand
    {
        $titleRecoveryData = Arr::only($data, [
            'vehicle_id',
            'vehicle_owner_id',
            'deposit_id',
            'comment'
        ]);

        $titleRecoveryData += [
            'demand_id' => $demand->id,
            'status' => Status::pending->name,
        ];

        $titleRecovery = $this->repository->storeOrUpdate($titleRecoveryData);

        $demand->update([
            'model_type' => $titleRecovery::class,
            'model_id' => $titleRecovery->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model|Demand
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {
            if ($treatment->pre_validated_at){
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);
                $demand->model->update(['status' => Status::validated->name]);
                $demand->update(['status' => Status::validated->name]);

                $demand->model->createCertificate('certificates.title-recovery');

                // $filename = 'CERTIFICAT_REPRISE_DE_TITRE_VEHICULE_' . $demand->model->vehicle->vin . '.pdf';
                //$certificate = $demand->model->generateCertificate($filename);
                //file_put_contents(public_path('storage/' . $filename), $certificate);
                // Notification::send($demand->model->vehicleOwner, new NotificationSender(NotificationNames::TITLE_DEPOSIT_VALIDATED, ['mail'], ['attachment' => $filename, 'reference' => $demand->reference]));
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
}
