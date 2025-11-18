<?php

namespace App\Services\Mutation;

use App\Consts\NotificationNames;
use App\Enums\ReasonEnum;
use App\Enums\Status;
use App\Interfaces\DemandServiceAdapter;
use App\Models\Institution\Institution;
use App\Models\Mutation;
use App\Models\Order\Demand;
use App\Models\OwnerHistory;
use App\Models\SaleDeclaration;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use App\Notifications\NotificationSender;
use App\Repositories\Crud\CrudRepository;
use App\Services\VehicleOwnerService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Ntech\UserPackage\Models\Identity;

class MutationServiceAdapter implements DemandServiceAdapter
{
    use CrudRepositoryTrait;

    private TreatmentService $treatmentService;
    private VehicleOwnerService $ownerService;
    private CrudRepository $ownerHistoryRepo;
    public function __construct()
    {
        $this->initRepository(Mutation::class);
        $this->ownerHistoryRepo = new CrudRepository(OwnerHistory::class);
        $this->treatmentService = new TreatmentService;
        $this->ownerService = new VehicleOwnerService;
    }

    public function initDemand(Demand $demand, array $data): Model
    {

        $mutationData = Arr::only($data, [
            'sale_declaration_reference',
            'comment',
            'vehicle_owner_id',
            'vehicle_id',
        ]);

        $saleDeclaration = SaleDeclaration::where('reference',$data['sale_declaration_reference'])->first();
        if (!$saleDeclaration->new_owner_id) {
            $identity = $saleDeclaration?->new_owner_npi ? Identity::where('npi',$saleDeclaration?->new_owner_npi)->first() : Institution::where('ifu',$saleDeclaration?->new_owner_ifu)->first();
            $newOwner = $saleDeclaration?->new_owner_npi ? VehicleOwner::where('identity_id',$identity->id)->first() : VehicleOwner::where('institution_id',$identity->id)->first();
            $saleDeclaration->update([
                'new_owner_id' => $newOwner->id
            ]);
        }
        $mutationData += [
            'demand_id' => $demand->id,
            'gray_card_id' => $demand->vehicle->grayCard->id,
            'sale_declaration_id' => $saleDeclaration->id,
            'new_owner_id' => $saleDeclaration->new_owner_id,
        ];
        $mutation = $this->repository->storeOrUpdate($mutationData);

        $demand->update([
            'model_type' => $mutation::class,
            'model_id' => $mutation->id,
        ]);

        return $demand;
    }

    public function validate(Demand $demand): Model
    {
        $treatment = $demand->activeTreatment;
        $model = $demand->model;
        DB::beginTransaction();
        try
        {
            $this->ownerHistoryRepo->store([
                'vehicle_owner_id' => $model->vehicle_owner_id,
                'vehicle_id' => $model->vehicle_id,
                'reason' => ReasonEnum::mutated->name
            ]);

            $model->grayCard->update([
                'vehicle_owner_id' => $model->new_owner_id,
            ]);

            $model->vehicle->update([
                'vehicle_owner_id' => $model->new_owner_id,
            ]);

            $treatment->update([
                'validated_by' => getOnlineProfile()?->id,
                'validated_at' => now()
            ]);
            $demand->update(['status' => Status::validated->name]);

            DB::commit();
            return $demand->refresh();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    public function submit(Demand $demand): Model|Demand
    {
        return $this->treatmentService->submitDemand($demand);
    }

    public function generateUniqueReference()
    {
        do{
            $reference = generateReference('MT');
        }while(SaleDeclaration::where('reference', $reference)->exists());

        return $reference;
    }
}
