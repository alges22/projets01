<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Requests\vehicleAdministrativeStatusRequest;
use App\Models\Account\Declarant;
use App\Models\FinancialInstitution\FinancialInstitution;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Repositories\VehicleAdministrativeStatusRepository;
use App\Services\Treatment\TreatmentService;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @property TreatmentService $treatmentService
 */

class VehicleAdministrativeStatusService
{
    use CrudRepositoryTrait, UploadFile;

    private VehicleAdministrativeStatusRepository $vehicleAdministrativeRepository;

    public function __construct()
    {
        $this->initRepository(VehicleAdministrativeStatus::class);
        $this->vehicleAdministrativeRepository = new VehicleAdministrativeStatusRepository;
        $this->treatmentService = new TreatmentService;
    }

    public function create()
    {
        //
    }

    public function store(VehicleAdministrativeStatusRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (!empty($request->declarant_id) && ($request->vehicle_id))
            {
                $vehicle = Vehicle::find($request->vehicle_id);
                $declarant = Declarant::find($request->declarant_id);
                $data = $data + [
                        'declaration_code' => $this->generateStatusCode(),
                        'vehicle_owner_id' => $vehicle->owner_id,
                        'financial_institution_id' => $declarant->financial_institution_id,
                        'status' => Status::pending->name,
                ];
                unset($data['documents']);
                $vehicleAdministrativeStatus = VehicleAdministrativeStatus::create($data);

                if ($request->documents) {
                    foreach ($request->documents as $document) {
                        $fileInfo = $this->saveFile($document['file'], "vehicle_administrative_status");
                        if ($fileInfo)
                        {
                            $this->createFile([
                                'path' => $fileInfo,
                                'model_type' => $vehicleAdministrativeStatus::class,
                                'model_id' => $vehicleAdministrativeStatus->id,
                                'file_type_id' => $document['document_type_id'],
                            ]);
                        }
                    }
                }
                DB::commit();
                return $vehicleAdministrativeStatus->refresh();
            }
        }catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    public function update(VehicleAdministrativeStatus $vehicleAdministrativeStatus, $data, $request = null)
    {
        DB::beginTransaction();
        try {
            $vehicleAdministrativeStatus = $this->repository->update($vehicleAdministrativeStatus, $data);

            if ($request?->has('documents') && $imagePaths = $this->saveMultipleFiles($request, 'vehicle_administrative_status', "documents")){
                foreach ($imagePaths as $imagePath)
                {
                    $this->createFile([
                        "path" => $imagePath,
                        "model_type" => $vehicleAdministrativeStatus::class,
                        "model_id" => $vehicleAdministrativeStatus->id,
                    ]);
                }
            }

            DB::commit();
            return $vehicleAdministrativeStatus->refresh();
        }catch(\Exception $exception){
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    public function destroy(Model $model)
    {
        if ($model->status == Status::pending->name)
        {
            return response($this->repository->destroy($model));
        }
    }

    public function generateStatusCode()
    {
        $reference = generateReference("VAS-");

        return VehicleAdministrativeStatus::where('declaration_code', $reference)->exists() ? $this->generateStatusCode() : $reference;
    }

    public function calculateDemandCost($demandId)
    {
        $demand = $this->repository->model
            ->newQuery()
            ->with('service')
            ->find($demandId);
        $totalCost = $demand->service->cost ? $demand->service->cost : $demand->service->type->cost;

        return $totalCost;
    }

    public function submitDemand($demandId)
    {
        $demand = $this->repository->find($demandId);
        //init treatment
        $treatment  = $demand->treatments()->create();
        $demand->update([
            'status' => Status::submitted->name,
            'submitted_at' => now(),
            'active_treatment_id' => $treatment->id
        ]);

        $demand->update([
            'status' => Status::submitted->name,
            'submitted_at' => now()
        ]);

        //assign automatically to service
        if (serviceExist( $treatment->model->service->target_organization_id)){
            $this->treatmentService->assignDemandToService([
                'treatment_id' => $treatment->id,
                'service_id' =>  $treatment->model->service->target_organization_id
            ]);
        }
        /*
            sendMail(
                null,
                $demand->vehicleOwner->identity,
                NotificationNames::PLATE_DUPLICATE_SUBMITTED,
                ['reference' => $demand->reference]
            );*/

        return $demand->refresh();
    }

    public function getDeclarerDemands($declarer, bool $paginate = true, $relations = []): mixed
    {
        $query = $this->repository
            ->model
            ->newQuery()
            ->select()
            ->where('declarant_id', $declarer)
            ->with($relations)
            ->orderByDesc('created_at')
            ->filter();

        return $query->paginate(request()->input('per_page',15));
    }


}
