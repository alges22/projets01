<?php

namespace App\Repositories;

use App\Consts\Roles;
use App\Models\Auth\Profile;
use App\Models\DemandOtp;
use App\Models\Institution\Institution;
use App\Models\Pledge;
use App\Models\PledgeLift;
use App\Models\PledgeLiftTreatment;
use App\Models\SimvebFile;
use App\Models\PledgeTreatment;
use App\Models\Vehicle\Vehicle;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\PledgeService;
use App\Services\VehicleOwnerService;
use App\Services\VehicleService;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Enums\InstitutionTypesEnum;
use App\Models\Vehicle\VehicleOwner;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PledgeRepository extends AbstractCrudRepository
{
    use UploadFile;
    private VehicleService $vehicleService;
    private VehicleOwnerService $ownerService;
    private PledgeService $pledgeService;
    private $authProfile;
    private array $validStatuses;
    private array $specificData;
    private string $errorMessage;

    public function __construct()
    {
        parent::__construct(Pledge::class);
        $this->vehicleService = new VehicleService;
        $this->ownerService = new VehicleOwnerService;
        $this->pledgeService = new PledgeService;
        $this->authProfile = getOnlineProfile();
        $this->validStatuses = [];
        $this->specificData = [];
        $this->errorMessage = "Impossible, d'effectuer cette action";
    }

    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        $query = $this->model->newQuery()->select()->orderBy('pledges.created_at', 'desc')->with($relations);
        $institutionProfiles = [
            ProfileTypesEnum::bank->name,
            ProfileTypesEnum::distributor->name
        ];

        switch ($this->authProfile->type->code) {
            case ProfileTypesEnum::anatt->name:
            case ProfileTypesEnum::court->name:
                $query->filter();
                break;

            case ProfileTypesEnum::bank->name:
                $query->where('institution_emitted_id', $this->authProfile->institution_id)
                    ->orWhere('financial_institution', $this->authProfile->institution_id)
                ->filter();
                break;

            case in_array($this->authProfile->type->code, $institutionProfiles):
                $query->where('institution_emitted_id', $this->authProfile->institution_id)->filter();
                break;

            default:
                abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Impossible d'effectuer cette action avec le profil actuel");
        }

        return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
    }


    public function create()
    {
        return [
            'financial_institutions' => Institution::whereHas('type', function ($query) {
                $query->where('name', InstitutionTypesEnum::financial_institution->name);
            })->select(['id','acronym'])->get(),

            'courts' => Institution::whereHas('type', fn($query) =>
                $query->where('name', InstitutionTypesEnum::ministry_justice->name)
            )->select(['id', 'acronym', 'name'])->get(),
        ];
    }


    public function getClerkByCourt()
    {
        $courtId = request()->court;
        $clerks = Profile::whereHas('type', fn($query) =>
                $query->where('id', $courtId)
            )
            ->whereHas('roles', function ($query) {
                $query->where('name', Roles::CLERK);
            })->get();

        $formattedClerks = $clerks->map(function ($clerk) {
            return [
                'id' => $clerk->id,
                'clerk' => $clerk->identity->firstname.' '.$clerk->identity->lastname,
                'institution' => $clerk->institution?->name,
                'suspended' => $clerk->suspended,
            ];
        });

        return [
            'clerks' => $formattedClerks,
        ];
    }


    public function showVehicleAndOwnerByVin()
    {
        $requestData = [
            'vin' => request()->vin,
            'customs_reference' => request()->customs_ref
        ];

        $vehicle = $this->vehicleService->showVehicleByVin($requestData);

        if (!$vehicle['success']) {
            abort(ResponseAlias::HTTP_NOT_FOUND, "Ce véhicule n'existe pas !");
        }

        $ownerId = Vehicle::where('vin', $requestData['vin'])->value('owner_id');

        if ($ownerId) {
            $ownerNpi = VehicleOwner::with('identity:id,npi')->find($ownerId);
            $owner = $this->ownerService->getOwnerByNpi($ownerNpi->identity?->npi);
                $data = array_merge($vehicle, ['owner' => $owner]);
        } else {
            abort(ResponseAlias::HTTP_NOT_FOUND, "Ce véhicule n'est pas immatriculé!");
        }

        return $data;
    }


    public function store(array $data, $request = null): Model|null
    {
        DB::beginTransaction();
        try {
            $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $data['vin']]);
            $vehicleOwner = VehicleOwner::where('id', $vehicle->owner_id)->first();

            if(!$vehicleOwner) {
                abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Ce véhicle n'est pas immatriculé");
            }

            $clerk = $this->pledgeService->getClerkProfileInSameCity($this->authProfile->identity->city);
            $anattProfile = $this->pledgeService->getAnattProfile();
            $bankProfile = Arr::has($data, 'financial_institution') ? $this->pledgeService->getBankProfile($data['financial_institution']) : null;

            $pledgeData = [
                'vehicle_id' => $vehicle?->id,
                'author_id' => $this->authProfile->id,
                'vehicle_owner_id' => $vehicleOwner?->id,
                'institution_emitted_id' => $this->authProfile->institution_id,
                'reference' => generateReference('PLG'),
                'financial_institution' => Arr::get($data, 'financial_institution', null),
                'status' => Status::emitted->name,
            ];

            $pledge = parent::store($pledgeData, $request);

            if ($request->filled('authorization_id')){
                DemandOtp::query()
                    ->where('id', $request->authorization_id)
                    ->update([
                        'model_id' => $pledge->id,
                        'model_type' => $pledge::class,
                    ]);
                $pledge->update(['otp_verified' => true]);
            }

            $pledgeTreatment = PledgeTreatment::create([
                'emitted_at' => now(),
                'pledge_id' => $pledge->id,
                'affected_to_anatt_at' => now(),
                'affected_to_clerk_at' => now(),
                'affected_to_clerk' => $clerk->id,
                'treated_by' => $this->authProfile->id,
                'affected_to_anatt' => $anattProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'affected_to_institution_at' => $pledgeData['financial_institution'] ? now() : null,
                'affected_to_institution' => $bankProfile->id ?? null,
                'status' => Status::emitted->name,
            ]);

            $pledge->pledge_treatment_id = $pledgeTreatment->id;
            $pledge->save();

            $documents = Arr::pull($data, 'pledge_file');

            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "pledge");
                    $pledgeTreatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $pledgeTreatment::class,
                        'model_id' => $pledgeTreatment->id,
                        'type' => SimvebFile::FILE,
                    ]);
                }
            }
            DB::commit();
            return $pledge;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function update(Model $model, array $data, $request = null): Model
    {
        $this->validStatuses = [
            Status::institution_rejected->name,
            Status::justice_rejected->name,
            Status::anatt_rejected->name
        ];

        if (!in_array($model->status, $this->validStatuses)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, $this->errorMessage);
        }

        $this->pledgeService->checkIfProfileExists($model->author_id);
        $bankProfile = Arr::has($data, 'financial_institution') ? $this->pledgeService->getBankProfile($data['financial_institution']) : null;

        DB::beginTransaction();
        try {

            $pledgeTreatmentData = [
                'remitted_at' => now(),
                'pledge_id' => $model->id,
                'treated_by' => $this->authProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'institution_remitted_id' => $this->authProfile->institution_id,
                'affected_to_anatt' => $model->activeTreatment->affected_to_anatt,
                'affected_to_clerk' => $model->activeTreatment->affected_to_clerk,
                'affected_to_clerk_at' => $model->activeTreatment->affected_to_clerk_at,
                'affected_to_anatt_at' => $model->activeTreatment->affected_to_anatt_at,
                'affected_to_institution_at' => Arr::has($data, 'financial_institution') ? now() : $model->activeTreatment->affected_to_institution_at,
                'affected_to_institution' => Arr::has($data, 'financial_institution') ? $bankProfile->id : $model->activeTreatment->affected_to_institution,
                'status' => Status::remitted->name,
            ];

            $pledgeTreatment = PledgeTreatment::create($pledgeTreatmentData);
            $model->status = Status::emitted->name;
            $model->pledge_treatment_id = $pledgeTreatment->id;
            $model->financial_institution = $data['financial_institution'] ?? $model->financial_institution;
            $model->can_update = false;
            $model->save();

            $documents = Arr::pull($data, 'pledge_file');

            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "pledge");
                    $pledgeTreatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $pledgeTreatment::class,
                        'model_id' => $pledgeTreatment->id,
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


    public function validatePledge(Model $model, array $data): Model
    {
        $this->validStatuses = [
            Status::emitted->name,
            Status::remitted->name,
            Status::justice_validated->name,
            Status::institution_validated->name,
        ];
        $this->errorMessage = "Dossier de gage déjà validé ou rejeté";
        $this->specificData = [
            'validated_at' => now(),
            'can_update' => false,
        ];

        $roles = $this->authProfile->roles->pluck('name')->toArray();
        $data = app(PledgeService::class)->validatePledgeForRole($roles, $model, $this->specificData);

        return $this->updatePledgeStatus($model, $data, $this->validStatuses, $this->errorMessage);
    }


    public function reject(Model $model, array $data): Model
    {
        $this->validStatuses = [
            Status::emitted->name,
            Status::institution_validated->name,
            Status::justice_validated->name
        ];
        $this->specificData = [
            'rejected_at' => now(),
            'rejected_reasons' => $data['rejected_reasons'],
            'can_update' => true,
        ];

        $roles = $this->authProfile->roles->pluck('name')->toArray();
        $data = app(PledgeService::class)->rejectPledgeForRole($roles, $model, $this->specificData);

        return $this->updatePledgeStatus($model, $data, $this->validStatuses, $this->errorMessage);
    }


    private function updatePledgeStatus($pledge, array $specificData, array $validStatuses, string $errorMessage)
    {
        if (!in_array($pledge->status, $validStatuses)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, $errorMessage);
        }

        DB::beginTransaction();
        try {

            $data = [
                'pledge_id' => $pledge->id,
                'status' => $specificData['status'],
                'treated_by' => $this->authProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'institution_remitted_id' => $pledge->institution_remitted_id,
                'rejected_reasons' => $specificData['rejected_reasons'] ?? null,
                'affected_to_clerk' => $pledge->activeTreatment->affected_to_clerk,
                'affected_to_anatt' => $pledge->activeTreatment->affected_to_anatt,
                'affected_to_clerk_at' => $pledge->activeTreatment->affected_to_clerk_at,
                'affected_to_anatt_at' => $pledge->activeTreatment->affected_to_anatt_at,
                'affected_to_institution' => $pledge->activeTreatment->affected_to_institution,
                'emitted_at' => $specificData['emitted_at'] ?? $pledge->activeTreatment->emitted_at,
                'affected_to_institution_at' => $pledge->activeTreatment->affected_to_institution_at,
                'remitted_at' => $specificData['remitted_at'] ?? $pledge->activeTreatment->remitted_at,
                'rejected_at' => $specificData['rejected_at'] ?? $pledge->activeTreatment->rejected_at,
                'validated_at' => $specificData['validated_at'] ?? $pledge->activeTreatment->validated_at,
            ];

            $data = array_merge($data, $specificData);

            $pledgeTreatment = PledgeTreatment::create($data);

            if ($pledge->activeTreatment->files) {
                foreach ($pledge->activeTreatment->files as $document) {
                    $pledgeTreatment->file()->create([
                        'path' => $document->path,
                        'model_type' => $document->model_type,
                        'model_id' => $pledgeTreatment->id,
                        'type' => $document->type,
                    ]);
                }
            }

            $pledge->status = $specificData['status'];
            $pledge->is_active = $specificData['is_active'] ?? false;
            $pledge->can_update = $specificData['can_update'] ?? false;
            $pledge->pledge_treatment_id = $pledgeTreatment->id;
            $pledge->save();

            DB::commit();
            return $pledge->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function liftPledge(Model $model, array $data, $request = null): Model
    {
        if (!$model) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Dossier non trouvé");
        }

        if ($model->treatments->first()->treated_by !== getOnlineProfile()->id) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habileté à effectuer cette action");
        }

        $pledgeLift = PledgeLift::query()->where('pledge_id', $model->id)->first();

        if ($pledgeLift) {
            $message = $pledgeLift->is_active
                ? "Impossible, ce dossier n'est plus sous gage"
                : "Impossible, une demande de levée de gage est déjà en cours de traitement";

            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, $message);
        }

        DB::beginTransaction();
        try {
            $clerk = $this->pledgeService->getClerkProfileInSameCity($this->authProfile->institution->town_id);
            $anattProfile = $this->pledgeService->getAnattProfile();

            $liftData = [
                "author_id" => $this->authProfile->id,
                "pledge_id" => $model->id,
                "institution_emitted_id" => $this->authProfile->institution_id,
                "status" => Status::emitted->name,
            ];

            $pledgeLift = PledgeLift::create($liftData);

            if ($request->filled('authorization_id')){
                DemandOtp::query()
                    ->where('id', $request->authorization_id)
                    ->update([
                        'model_id' => $pledgeLift->id,
                        'model_type' => $pledgeLift::class,
                    ]);
                $pledgeLift->update(['otp_verified' => true]);
            }

            $pledgeLiftTreatment = PledgeLiftTreatment::create([
                'emitted_at' => now(),
                'pledge_id' => $model->id,
                'pledge_lift_id' => $pledgeLift->id,
                'affected_to_anatt_at' => now(),
                'affected_to_clerk_at' => now(),
                'affected_to_clerk' => $clerk->id,
                'treated_by' => $this->authProfile->id,
                'affected_to_anatt' => $anattProfile->id,
                'treated_by_space' => $this->authProfile->space->type_label,
                'institution_treat_id' => $this->authProfile->institution_id,
                'status' => Status::emitted->name,
            ]);

            $documents = Arr::pull($data, 'pledge_file');
            if ($documents) {
                foreach ($documents as $document) {
                    $fileInfo = $this->saveFile($document, "pledge");
                    $pledgeLiftTreatment->file()->create([
                        'path' => $fileInfo,
                        'model_type' => $pledgeLiftTreatment::class,
                        'model_id' => $pledgeLiftTreatment->id,
                        'type' => SimvebFile::FILE,
                    ]);
                }
            }

            $pledgeLift->pledge_lift_treatment_id = $pledgeLiftTreatment->id;
            $pledgeLift->save();

            DB::commit();
            return $pledgeLift;
        }catch (\Exception $exception){
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }


    public function totalPledges()
    {
        $pledges = Pledge::query()->get();

        return ["total_pledges" => $pledges->count()];
    }


    public function activeClosePledges()
    {
        $pledges = Pledge::query()
            ->selectRaw("COUNT(CASE WHEN is_active = true AND status != ? THEN 1 END) as active_pledges", [Status::closed->name])
            ->selectRaw("COUNT(CASE WHEN is_active = false AND status != ? THEN 1 END) as treatment_pledges", [Status::closed->name])
            ->selectRaw("COUNT(CASE WHEN is_active = false AND status = ? THEN 1 END) as closed_pledges", [Status::closed->name])
            ->first();

        return [
            "active_pledges" => $pledges->active_pledges,
            "treatment_pledges" => $pledges->treatment_pledges,
            "closed_pledges" => $pledges->closed_pledges,
        ];
    }

    public function listLiftablePledges(bool $paginate = true, $relations = []): mixed
    {
        $liftablePledges = Pledge::query()->with($relations)
            ->where([
                ['is_active', true],
                ['can_update', false],
                ['status', Status::anatt_validated->name],
            ]);

        return $paginate ? $liftablePledges->paginate(request('per_page', 15)) : $liftablePledges->get();
    }
}
