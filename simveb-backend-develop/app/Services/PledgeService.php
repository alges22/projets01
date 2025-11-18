<?php

namespace App\Services;

use App\Consts\Roles;
use App\Enums\InstitutionTypesEnum;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Models\Auth\Profile;
use App\Models\DemandOtp;
use App\Models\Pledge;
use App\Models\PledgeTreatment;
use App\Models\Treatment\PrintOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function League\Uri\UriTemplate\first;
use function Ntech\ActivityLogPackage\Services\get;

class PledgeService
{
    public function getClerkProfileInSameCity($city)
    {
        $profiles = Profile::query()
            ->where('suspended', false)
            ->whereHas('roles', fn($query) =>
                $query->where('name', Roles::CLERK)
            )
            ->whereHas('institution', fn($query) =>
                $query->where([
                    ['profile_type_code', ProfileTypesEnum::court->name],
                    ['town_id', $city],
                ])
                ->whereHas('type', fn($subQuery) =>
                    $subQuery->where('name', InstitutionTypesEnum::ministry_justice->name)
                )
            )->get();

        return $profiles->isNotEmpty() ? $profiles->first() : Profile::query()
            ->where('suspended', false)
            ->whereHas('roles', fn($query) => $query->where('name', Roles::CLERK))
            ->whereHas('institution.type', fn($query) => $query->where('name', InstitutionTypesEnum::ministry_justice->name))
            ->whereHas('identity', fn($query) => $query->where('city', $city))
            ->inRandomOrder()
            ->first();
    }

    public function getAnattProfile()
    {
        $anattProfile = Profile::query()
            ->where('suspended', false)
            ->whereHas('roles', fn($query) =>
                $query->where('name', Roles::ADMIN)
            )
            ->whereHas('institution', fn($query) =>
                $query->where('profile_type_code', ProfileTypesEnum::anatt->name)
            ->whereHas('type', fn($subQuery) =>
                $subQuery->where('name', InstitutionTypesEnum::gov_institution->name)
            )
        )->inRandomOrder()->first();

        return $anattProfile;
    }

    public function getBankProfile($financialInstitutionId)
    {
        $bankProfile = Profile::query()
            ->where('suspended', false)
            ->whereHas('institution.type', fn($query) =>
                $query->where('name', InstitutionTypesEnum::financial_institution->name)
            )
            ->whereHas('institution', fn($query) =>
                $query->where('id', $financialInstitutionId)
        )->select('id')->first();

        return $bankProfile;
    }

    public function getAnattProfileToAffectedPledge()
    {
        $anatt = Profile::query()
            ->where('suspended', false)
            ->whereHas('roles', fn($query) =>
                $query->where('name', Roles::ADMIN)
            )->get();

        return $anatt;
    }


    public function affectationToClerk(Model $model ,array $data)
    {
        DB::beginTransaction();
        try {
            $profile = getOnlineProfile();

            $validStatuses = [Status::emitted->name, Status::institution_validated->name, Status::institution_rejected->name];

            if (!in_array($model->status, $validStatuses)) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Impossible d'affecter à un autre greffier");
            }

            $pledgeTreatment = PledgeTreatment::create([
                'pledge_id' => $model->id,
                'treated_by' => $profile->id,
                'treated_by_space' => $profile->space->type_label,
                'institution_treat_id' => $profile->institution_id,
                'emitted_at' => $model->activeTreatment->emitted_at,
                'remitted_at' => $model->activeTreatment->remitted_at,
                'rejected_at' => $model->activeTreatment->rejected_at,
                'validated_at' => $model->activeTreatment->validated_at,
                'affected_to_anatt' => $model->activeTreatment->affected_to_anatt,
                'institution_remitted_id' => $model->activeTreatment->institution_remitted_id,
                'affected_to_clerk' => $data['affected_to_clerk'] ?? $model->activeTreatment->affected_to_clerk,
                'affected_to_clerk_at' => $data['affected_to_clerk'] ? now() : $model->activeTreatment->affected_to_clerk_at,
                'affected_to_anatt_at' => $model->activeTreatment->affected_to_anatt_at,
                'affected_to_institution' => $model->activeTreatment->affected_to_institution,
                'affected_to_institution_at' => $model->activeTreatment->affected_to_institution_at,
                'status' => Status::affected_to_clerk->name,
            ]);

            $model->update([
                'pledge_treatment_id' => $pledgeTreatment->id,
            ]);

            DB::commit();

            return ['message' => 'Dossier affecté à un autre greffier'];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function checkIfProfileExists($affectedTo)
    {
        $profile = Profile::query()->where([['id', $affectedTo],['suspended', false]])->first();

        if (!$profile) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Ce profil n'est pas habilité à effectuer cette action");
        }

        $profiles = Profile::query()
            ->where('suspended', false)
            ->whereHas('institution', fn($query) =>
            $query->where('profile_type_code', $profile->type->code)
        )->where('id', $affectedTo)
        ->exists();

        if (!$profiles || $affectedTo !== getOnlineProfile()->id) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à traiter cette demande");
        }

        return $profiles;
    }

    private function validateByBank($pledge, $data)
    {
        $this->checkIfProfileExists($pledge->activeTreatment->affected_to_institution);

        if (!isset($pledge->financial_institution) && !isset($pledge->activeTreatment->affected_to_institution)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }

        if ($pledge->status !== Status::emitted->name) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }
        $data['status'] = Status::institution_validated->name;

        return $data;
    }

    private function validateByClerk($pledge, $data)
    {
        $this->checkIfProfileExists($pledge->activeTreatment->affected_to_clerk);

        if (!in_array($pledge->status, [Status::institution_validated->name, Status::emitted->name])) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à traiter cette demande");
        }

        if ($pledge->status === Status::emitted->name && isset($pledge->financial_institution)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "En attente de validation par la banque");
        }

        $data['status'] = Status::justice_validated->name;

        return $data;
    }

    private function validateByAdmin($pledge, $data)
    {
        $this->checkIfProfileExists($pledge->activeTreatment->affected_to_anatt);

        if ($pledge->is_active){
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Impossible, dossier clôturé ou sous gage");
        }elseif ($pledge->status !== Status::justice_validated->name) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "En attente de validation par le greffier ou une institution compétente");
        }
        $data['status'] = Status::anatt_validated->name;
        $data['is_active'] = true;

        return $data;
    }


    public function validatePledgeForRole($roles, $pledge, $data)
    {
        if (in_array(Roles::BANK, $roles)) {
            return $this->validateByBank($pledge, $data);
        } elseif (in_array(Roles::CLERK, $roles)) {
            return $this->validateByClerk($pledge, $data);
        } elseif (in_array(Roles::ADMIN, $roles)) {
            return $this->validateByAdmin($pledge, $data);
        } else {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }
    }


    private function rejectByBank($pledge, $specificData)
    {
        $this->checkIfProfileExists($pledge->activeTreatment->affected_to_institution);

        if (!isset($pledge->financial_institution) && $pledge->status !== Status::emitted->name) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "La demande n'est pas prête à être validée par la banque");
        }
        $specificData['status'] = Status::institution_rejected->name;

        return $specificData;
    }

    private function rejectByClerk($pledge, $specificData)
    {
        $this->checkIfProfileExists($pledge->activeTreatment->affected_to_clerk);

        if (isset($pledge->financial_institution) && $pledge->status !== Status::institution_validated->name) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "En attente de traitement par la banque");
        } elseif ($pledge->status !== Status::emitted->name) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }

        $specificData['status'] = Status::justice_rejected->name;

        return $specificData;
    }

    private function rejectByAdmin($pledge, $specificData)
    {
        $this->checkIfProfileExists($pledge->activeTreatment->affected_to_anatt);

        if ($pledge->status !== Status::justice_validated->name) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "En attente de traitement par le greffier ou une institution compétente");
        }
        $specificData['status'] = Status::anatt_rejected->name;

        return $specificData;
    }


    public function rejectPledgeForRole($roles, $pledge, $data)
    {
        if (in_array(Roles::BANK, $roles)) {
            return $this->rejectByBank($pledge, $data);
        } elseif (in_array(Roles::CLERK, $roles)) {
            return $this->rejectByClerk($pledge, $data);
        } elseif (in_array(Roles::ADMIN, $roles)) {
            return $this->rejectByAdmin($pledge, $data);
        } else {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Vous n'êtes pas habilité à effectuer cette action");
        }
    }

}
