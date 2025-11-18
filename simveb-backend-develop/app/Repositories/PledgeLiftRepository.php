<?php

namespace App\Repositories;

use App\Models\Pledge;
use App\Models\PledgeLift;
use App\Models\PledgeLiftTreatment;
use App\Models\SimvebFile;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\PledgeService;
use App\Services\VehicleOwnerService;
use App\Services\VehicleService;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PledgeLiftRepository extends AbstractCrudRepository
{
    use UploadFile;
    private VehicleService $vehicleService;
    private VehicleOwnerService $ownerService;
    private PledgeService $pledgeService;
    private $authProfile;

    public function __construct()
    {
        parent::__construct(PledgeLift::class);
        $this->vehicleService = new VehicleService;
        $this->ownerService = new VehicleOwnerService;
        $this->pledgeService = new PledgeService;
        $this->authProfile = getOnlineProfile();
    }


    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()->select()->orderBy('pledge_lifts.created_at', 'desc')->with($relations)
            ->filter();

        switch ($this->authProfile->type->code) {
            case ProfileTypesEnum::anatt->name:
            case ProfileTypesEnum::court->name:
            case ProfileTypesEnum::bank->name:
                $query->filter();
                break;

            default:
                abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible d'effectuer cette action avec le profil actuel");
        }

        return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
    }


    public function update(Model $model, array $data, $request = null): Model
    {
        if (!in_array($model->status, [Status::justice_rejected->name, Status::anatt_rejected->name])) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible, d'effectuer cette action");
        }

        $this->pledgeService->checkIfProfileExists($model->author_id);

        DB::beginTransaction();
        try {

            $dataTreatment = [
                'remitted_at' => now(),
                'pledge_lift_id' => $model->id,
                'treated_by' => $this->authProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'institution_remitted_id' => $this->authProfile->institution_id,
                'affected_to_anatt' => $model->activeTreatment->affected_to_anatt,
                'affected_to_clerk' => $model->activeTreatment->affected_to_clerk,
                'affected_to_clerk_at' => $model->activeTreatment->affected_to_clerk_at,
                'affected_to_anatt_at' => $model->activeTreatment->affected_to_anatt_at,
                'status' => Status::remitted->name,
            ];

            $pledgeLiftTreatment = PledgeLiftTreatment::create($dataTreatment);

            $model->status = Status::emitted->name;
            $model->pledge_lift_treatment_id = $pledgeLiftTreatment->id;
            $model->can_update = false;
            $model->save();

            $documents = Arr::pull($data, 'pledge_file');

            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "pledge");
                    $pledgeLiftTreatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $model::class,
                        'model_id' => $model->id,
                        'type' => SimvebFile::FILE,
                    ]);
                }
            }

            DB::commit();
            return $model->refresh();
        } catch (\Exception $exception) {

            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function validatePledgeLift(Model $model, array $data): Model
    {
        $validStatuses = [
            Status::emitted->name,
            Status::justice_validated->name,
        ];
        $specificData = [
            'validated_at' => now(),
            'can_update' => false,
        ];

        $roles = $this->authProfile->roles->pluck('name')->toArray();
        $data = app(PledgeService::class)->validatePledgeForRole($roles, $model, $specificData);

        if (!in_array($model->status, $validStatuses)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Dossier de gage déjà validé ou rejeté");
        }

        DB::beginTransaction();
        try {

            $pledgeLiftTreatmentData = [
                'pledge_lift_id' => $model->id,
                'status' => $data['status'],
                'is_active' => $data['is_active'] ?? false,
                'treated_by' => $this->authProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'institution_remitted_id' => $model->institution_remitted_id,
                'rejected_reasons' => $data['rejected_reasons'] ?? null,
                'affected_to_clerk' => $model->activeTreatment->affected_to_clerk,
                'affected_to_anatt' => $model->activeTreatment->affected_to_anatt,
                'affected_to_clerk_at' => $model->activeTreatment->affected_to_clerk_at,
                'affected_to_anatt_at' => $model->activeTreatment->affected_to_anatt_at,
                'emitted_at' => $data['emitted_at'] ?? $model->activeTreatment->emitted_at,
                'remitted_at' => $data['remitted_at'] ?? $model->activeTreatment->remitted_at,
                'rejected_at' => $data['rejected_at'] ?? $model->activeTreatment->rejected_at,
                'validated_at' => $data['validated_at'] ?? $model->activeTreatment->validated_at,
            ];

            $data = array_merge($pledgeLiftTreatmentData, $specificData);

            $pledgeLiftTreatment = PledgeLiftTreatment::create($data);

            $model->status = $data['status'];
            $model->is_active = $data['is_active'] ?? false;
            $model->can_update = $data['can_update'] ?? false;
            $model->pledge_lift_treatment_id = $pledgeLiftTreatment->id;
            $model->save();

            if ($model->is_active === true) {
                $pledge = Pledge::query()->where([
                    ['id', $model->pledge_id],
                    ['is_active', true]
                ])->first();
                $pledge->is_active = false;
                $pledge->status = Status::closed->name;
                $pledge->save();
            }

            if ($model->activeTreatment->files) {
                foreach ($model->activeTreatment->files as $document) {
                    $pledgeLiftTreatment->file()->create([
                        'path' => $document->path,
                        'model_type' => $document->model_type,
                        'model_id' => $pledgeLiftTreatment->id,
                        'type' => $document->type,
                    ]);
                }
            }

            DB::commit();
            return $model->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function rejectLift(Model $model, array $data): Model
    {
        $validStatuses = [
            Status::emitted->name,
            Status::justice_validated->name,
        ];
        $specificData = [
            'rejected_at' => now(),
            'rejected_reasons' => $data['rejected_reasons'],
            'can_update' => true,
        ];

        $roles = $this->authProfile->roles->pluck('name')->toArray();
        $data = app(PledgeService::class)->rejectPledgeForRole($roles, $model, $specificData);

        if (!in_array($model->status, $validStatuses)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible, d'effectuer cette action");
        }

        DB::beginTransaction();
        try {

            $pledgeLiftTreatmentData = [
                'pledge_lift_id' => $model->id,
                'status' => $data['status'],
                'is_active' => $data['is_active'] ?? false,
                'treated_by' => $this->authProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'institution_remitted_id' => $model->institution_remitted_id,
                'rejected_reasons' => $data['rejected_reasons'] ?? null,
                'affected_to_clerk' => $model->activeTreatment->affected_to_clerk,
                'affected_to_anatt' => $model->activeTreatment->affected_to_anatt,
                'affected_to_clerk_at' => $model->activeTreatment->affected_to_clerk_at,
                'affected_to_anatt_at' => $model->activeTreatment->affected_to_anatt_at,
                'emitted_at' => $data['emitted_at'] ?? $model->activeTreatment->emitted_at,
                'remitted_at' => $data['remitted_at'] ?? $model->activeTreatment->remitted_at,
                'rejected_at' => $data['rejected_at'] ?? $model->activeTreatment->rejected_at,
                'validated_at' => $data['validated_at'] ?? $model->activeTreatment->validated_at,
            ];

            $data = array_merge($pledgeLiftTreatmentData, $specificData);

            $pledgeLiftTreatment = PledgeLiftTreatment::create($data);

            $model->status = $data['status'];
            $model->is_active = $data['is_active'] ?? false;
            $model->can_update = $data['can_update'] ?? false;
            $model->pledge_lift_treatment_id = $pledgeLiftTreatment->id;
            $model->save();

            if ($model->activeTreatment->files) {
                foreach ($model->activeTreatment->files as $document) {
                    $pledgeLiftTreatment->file()->create([
                        'path' => $document->path,
                        'model_type' => $document->model_type,
                        'model_id' => $pledgeLiftTreatment->id,
                        'type' => $document->type,
                    ]);
                }
            }

            DB::commit();
            return $model->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
