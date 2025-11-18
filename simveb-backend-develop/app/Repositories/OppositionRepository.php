<?php

namespace App\Repositories;

use App\Consts\Roles;
use App\Models\Auth\Role;
use App\Models\Config\TitleReason;
use App\Models\Config\TitleReasonType;
use App\Models\DemandOtp;
use App\Models\Opposition;
use App\Models\OppositionTreatment;
use App\Models\SimvebFile;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Enums\ProfileTypesEnum;
use App\Enums\ReasonTypeEnum;
use App\Enums\Status;
use App\Services\OppositionService;
use App\Services\VehicleOwnerService;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Identity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OppositionRepository extends AbstractCrudRepository
{
    use UploadFile;
    use CrudRepositoryTrait;
    private $authProfile;
    private array $specificData;
    private array $validStatuses;
    private string $errorMessage;

    public function __construct()
    {
        parent::__construct(Opposition::class);
        $this->authProfile = getOnlineProfile();
        $this->service = new VehicleOwnerService;
        $this->initRepository(Opposition::class);
        $this->oppositionService = new OppositionService;
        $this->validStatuses = [];
        $this->specificData = [];
        $this->errorMessage = "Impossible, d'effectuer cette action";
    }


    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $queries = $this->model->newQuery()->orderBy('oppositions.created_at', 'desc')->with($relations)->where('institution_id', $this->authProfile->institution_id)->filter();

        return $paginate ? $queries->paginate(request('per_page', 15)) : $queries->get();
    }


    public function create()
    {
        return [
            'reasons' => TitleReason::whereHas('reasonType', function ($query) {
                $query->where('name', ReasonTypeEnum::opposition->name);
            })->select(['id', 'label'])->get(),
        ];
    }


    public function store(array $data, $request = null): Model|null
    {
        DB::beginTransaction();
        try {

            $owner = strlen($data['npi_or_ifu']) !== 10
                ? $this->service->getOwnerByIfu($data['npi_or_ifu'])
                : $this->service->getOwnerByNpi($data['npi_or_ifu']);

            $vehicles = $data['vehicles'];

            $oppositionData = [
                'owner_id' => $owner?->id,
                'status' => Status::opposition_emitted->name,
                'author_id' => $this->authProfile->id,
                'institution_id' => $this->authProfile->institution_id,
                'reason_for_opposition' => $data['reason_for_opposition'],
                'reference' => generateReference("OPPO"),
            ];

            $opposition = parent::store($oppositionData, $request);

            $opposition->vehicles()->attach($vehicles);

            $clerk = $this->oppositionService->getClerkProfileInSameCourt($oppositionData['institution_id']);
            $judge = $this->oppositionService->getJudgeProfileInSameCourt($oppositionData['institution_id']);

            $treatment = OppositionTreatment::create([
                'status' => Status::opposition_emitted->name,
                'treated_by' => $this->authProfile->id,
                'institution_id' => $this->authProfile->institution_id,
                'opposition_id' => $opposition->id,
                'emitted_at' => now(),
                'affected_to_clerk' => $clerk?->id,
                'affected_to_clerk_at' => now(),
                'affected_to_judge' => $this->authProfile->id,
                'affected_to_judge_at' => now(),
            ]);

            $opposition->update(['treatment_id' => $treatment->id]);

            $documents = Arr::pull($data, 'opposition_file');

            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "opposition");
                    $treatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $treatment::class,
                        'model_id' => $treatment->id,
                        'type' => SimvebFile::FILE,
                    ]);
                }
            }

            DemandOtp::query()
                ->where('id', $request->authorization_id)
                ->update([
                    'model_id' => $opposition->id,
                    'model_type' => $opposition::class,
                ]);
            $opposition->update(['otp_verified' => true]);

            DB::commit();
            return $opposition;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function showVehicles(array $data, $request = null)
    {
        $owner = strlen($data['npi_or_ifu']) !== 10
            ? $this->service->getOwnerByIfu($data['npi_or_ifu'])
            : $this->service->getOwnerByNpi($data['npi_or_ifu']);

        if (!$owner) {
            return $this->errorResponse("Npi ou l'ifu renseigné n'existe pas !", ResponseAlias::HTTP_NOT_FOUND);
        }

        $vehicles = Vehicle::with('immatriculation')->where('owner_id', $owner ? $owner->id : null)->get();

        if ($vehicles->isEmpty()) {
            abort(ResponseAlias::HTTP_NOT_FOUND, "Aucun véhicule trouvé pour ce propriétaire");
        }

        $formattedVehicles = $vehicles->map(fn($vehicle) => [
            'id' => $vehicle->id,
            'image' => $vehicle->vehicle_image,
            'vin' => $vehicle->vin,
            'plate_number' => $vehicle->immatriculation->number_label ?? null,
        ]);

        return $formattedVehicles;
    }


    public function updateOpposition(Model $model, array $data): Model
    {
        $opposition = $this->model::with('activeTreatment')->findOrFail($model->id);
        $this->validStatuses = [Status::clerk_rejected->name, Status::judge_rejected->name];

        if (!in_array($opposition->status, $this->validStatuses)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, $this->errorMessage);
        }

        $this->checkAuthorization(
            $opposition, $opposition->status, Status::judge_rejected->name,
            Status::clerk_rejected->name, Status::opposition_lifted_emitted->name, Status::opposition_emitted->name
        );

        DB::beginTransaction();
        try {
            $treatment = OppositionTreatment::create([
                'remitted_at' => now(),
                'opposition_id' => $opposition->id,
                'treated_by' => $this->authProfile->id,
                'institution_id' => $this->authProfile->institution_id,
                'lifted_at' => $opposition->activeTreatment->lifted_at ?? null,
                'emitted_at' => $opposition->activeTreatment->emitted_at ?? null,
                'rejected_at' => $opposition->activeTreatment->rejected_at ?? null,
                'validated_at' => $opposition->activeTreatment->validated_at ?? null,
                'affected_to_judge' => $opposition->activeTreatment->affected_to_judge ?? null,
                'affected_to_clerk' => $opposition->activeTreatment->affected_to_clerk ?? null,
                'affected_to_clerk_at' => $opposition->activeTreatment->affected_to_clerk_at ?? null,
                'affected_to_judge_at' => $opposition->activeTreatment->affected_to_judge_at ?? null,
                'status' => Status::remitted->name,
            ]);

            $opposition->update([
                'treatment_id' => $treatment->id,
                'status' => $this->specificData['status'],
                'reason_for_opposition' => $data['reason_for_opposition'],
            ]);

            if (isset($data['vehicles'])) {
                $opposition->vehicles()->sync($data['vehicles']);
            }

            $documents = Arr::pull($data, 'opposition_file');
            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "opposition");
                    $treatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $treatment::class,
                        'model_id' => $treatment->id,
                        'type' => SimvebFile::FILE,
                    ]);
                }
            }

            DB::commit();
            return $opposition->refresh();
        } catch (\Exception $exception) {

            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function delete(Model $model)
    {
        DB::beginTransaction();
        try {
            if (!in_array($model->status, [Status::opposition_emitted->name, Status::opposition_lifted_emitted->name])) {
                throw new HttpException(ResponseAlias::HTTP_NOT_FOUND, $this->errorMessage);
            }

            $model->vehicles()->sync([]);
            $opposition = $this->repository->destroy($model);

            DB::commit();
            return $opposition->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }


    public function validateOpposition(Model $model): Model
    {
        $this->validStatuses = [Status::opposition_emitted->name, Status::opposition_lifted_emitted->name];
        $this->errorMessage = "Dossier d'opposition en cours de traitement";

        $this->specificData = ['validated_at' => now(),];

        $isActive = $this->checkAuthorization(
            $model, $model->status,
            Status::opposition_emitted->name, Status::opposition_lifted_emitted->name,
            Status::clerk_validated->name, Status::judge_validated->name,
        );
        $this->specificData['is_active'] = $isActive;

        return $this->updateOppositionStatus($model, $this->specificData, $this->validStatuses, $this->errorMessage);
    }


    public function lift(Model $model, array $data): Model
    {
        $this->validStatuses = [Status::clerk_validated->name];
        $this->errorMessage = "Dossier d'opposition en cours de traitement";

        $currentStatus = $model->status;

        $this->specificData = [
            'lifted_at' => now(),
            'opposition_file' => $data['opposition_file']
        ];

        if ($this->authProfile->hasRole([Roles::CLERK]) && $model->activeTreatment->affected_to_clerk === $this->authProfile->id && $currentStatus === Status::clerk_validated->name) {
                $this->specificData['status'] = Status::opposition_lifted_emitted->name;
        }else{
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }

        return $this->updateOppositionStatus($model, $this->specificData, $this->validStatuses, $this->errorMessage);
    }


    public function reject(Model $model, array $data): Model
    {
        $this->validStatuses = [Status::opposition_emitted->name, Status::opposition_lifted_emitted->name];
        $this->specificData = [
            'rejected_reason' => $data['rejected_reason'],
            'rejected_at' => now(),
        ];

        $this->checkAuthorization(
            $model, $model->status, Status::opposition_emitted->name,
            Status::opposition_lifted_emitted->name, Status::clerk_rejected->name, Status::judge_rejected->name
        );

        return $this->updateOppositionStatus($model, $this->specificData, $this->validStatuses, $this->errorMessage);
    }


    protected function checkAuthorization($opposition, $currentStatus, $clerkStatus, $judgeStatus, $clerkFinalStatus, $judgeFinalStatus)
    {
        if ($this->authProfile->hasRole([Roles::CLERK])
            && $opposition->activeTreatment->affected_to_clerk === $this->authProfile->id
            && $currentStatus === $clerkStatus) {

            $this->specificData['status'] = $clerkFinalStatus;
            return true;

        } elseif ($this->authProfile->hasRole([Roles::INVESTIGATING_JUDGE])
            && $opposition->activeTreatment->affected_to_judge === $this->authProfile->id
            && $currentStatus === $judgeStatus) {

            $this->specificData['status'] = $judgeFinalStatus;
            return false;

        } else {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }
    }


    private function updateOppositionStatus($opposition, array $specificData, array $validStatuses, string $errorMessage)
    {
        if (!in_array($opposition->status, $validStatuses)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, $errorMessage);
        }

        DB::beginTransaction();
        try {
            $data = [
                'opposition_id' => $opposition->id,
                'status' => $specificData['status'],
                'treated_by' => $this->authProfile->id,
                'institution_id' => $opposition->institution_id,
                'lifted_at' => $specificData['lifted_at'] ?? $opposition->activeTreatment->lifted_at,
                'emitted_at' => $specificData['emitted_at'] ?? $opposition->activeTreatment->emitted_at,
                'rejected_at' => $specificData['rejected_at'] ?? $opposition->activeTreatment->rejected_at,
                'remitted_at' => $specificData['remitted_at'] ?? $opposition->activeTreatment->remitted_at,
                'validated_at' => $specificData['validated_at'] ?? $opposition->activeTreatment->validated_at,
                'affected_to_judge' => $specificData['affected_to_judge'] ?? $opposition->activeTreatment->affected_to_judge,
                'affected_to_clerk' => $specificData['affected_to_clerk'] ?? $opposition->activeTreatment->affected_to_clerk,
                'affected_to_clerk_at' => $specificData['affected_to_clerk_at'] ?? $opposition->activeTreatment->affected_to_clerk_at,
                'affected_to_judge_at' => $specificData['affected_to_judge_at'] ?? $opposition->activeTreatment->affected_to_judge_at,
            ];

            $data = array_merge($data, $specificData);

            $oppositionTreatment = OppositionTreatment::create($data);

            $opposition->update([
                'treatment_id' => $oppositionTreatment->id,
                'status' => $specificData['status'],
                'is_active' => $specificData['is_active'] ?? $opposition->is_active,
            ]);

            $documents = Arr::pull($data, 'opposition_file');
            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "opposition");
                    $oppositionTreatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $oppositionTreatment::class,
                        'model_id' => $oppositionTreatment->id,
                        'type' => SimvebFile::FILE,
                    ]);
                }
            }else{
                foreach ($opposition->activeTreatment->files as $document) {
                    $oppositionTreatment->file()->create([
                        'path' => $document->path,
                        'model_type' => $document->model_type,
                        'model_id' => $oppositionTreatment->id,
                        'type' => $document->type,
                    ]);
                }
            }

            if ($opposition->is_active === false && $opposition->status === Status::judge_validated->name) {
                $opposition->update(['status' => Status::closed->name]);
                $data['status'] = Status::closed->name;
                OppositionTreatment::create($data);
            }

            DB::commit();
            return $opposition->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function oppositionTotal()
    {
        $oppositions = Opposition::query()->get();

        return ['total_oppositions' => $oppositions->count()];
    }


    public function activeCloseOpposition()
    {
        $oppositions = Opposition::query()
            ->selectRaw("COUNT(CASE WHEN is_active = true AND status != ? THEN 1 END) as active_oppositions", [Status::closed->name])
            ->selectRaw("COUNT(CASE WHEN is_active = false AND status != ? THEN 1 END) as treatment_oppositions", [Status::closed->name])
            ->selectRaw("COUNT(CASE WHEN is_active = false AND status = ? THEN 1 END) as closed_oppositions", [Status::closed->name])
            ->first();

        return [
            "active_pledges" => $oppositions->active_oppositions,
            "treatment_pledges" => $oppositions->treatment_oppositions,
            "closed_pledges" => $oppositions->closed_oppositions,
        ];
    }

}
