<?php

namespace App\Services\Title;

use App\Enums\DemandUpdatesTypeEnum;
use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Order\Demand;
use App\Models\Title\TitleDeposit;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TitleDepositServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    private TreatmentService $treatmentService;


    public function __construct()
    {
        $this->initRepository(TitleDeposit::class);
        $this->treatmentService = new TreatmentService;
    }

    public function initDemand(Demand $demand, array $data): Model|Demand
    {
        $titleDepositData = Arr::only($data, [
            'vehicle_id',
            'vehicle_owner_id',
            'title_reason_id'
        ]);

        $titleDepositData += [
            'demand_id' => $demand->id,
            'status' => Status::pending->name,
        ];

        $titleDeposit = $this->repository->storeOrUpdate($titleDepositData);

        $demand->update([
            'model_type' => $titleDeposit::class,
            'model_id' => $titleDeposit->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model|Demand
    {
        $treatment = $demand->activeTreatment;
        DB::beginTransaction();
        try {
            if ($treatment->pre_validated_at) {
                $treatment->update([
                    'validated_by' => getOnlineProfile()?->id,
                    'validated_at' => now()
                ]);
                $demand->model->update(['status' => Status::validated->name]);
                $demand->update(['status' => Status::validated->name]);

                $demand->model->createCertificate('certificates.title-deposit-certificate');

                // TO-DO: Genarate the pdf certifcate of an title deposit
                // $filename = 'CERTIFICAT_DEPOT_DE_TITRE_VEHICULE_' . $demand->model->vehicle->vin . '.pdf';
                // $certificate = $demand->model->generateCertificate($filename, true);
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
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function submit(Demand $demand): Model|Demand
    {
        return $this->treatmentService->submitDemand($demand);
    }

    public function update(Demand $demand, array $data)
    {
        $titleDeposit = $this->repository->findWhere(['demand_id' => $demand->id]);

        saveDemandUpdateHistory([
            'old_value' => $titleDeposit->title_reason_id,
            'new_value' => $data['title_reason_id'],
            'type' => DemandUpdatesTypeEnum::title_reason->name,
            'demand_id' => $demand->id,
            'model_type' => $titleDeposit::class,
            'model_id' => $titleDeposit->id,
        ]);

        return $this->repository->update($titleDeposit, $data);
    }
}
