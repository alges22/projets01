<?php

namespace Ntech\UserPackage\Repositories;


use App\Exceptions\SecureDeleteException;
use App\Services\InvitationService;
use Ntech\UserPackage\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @property InvitationService $invitationService
 */
class StaffRepository
{
    private IdentityRepository $identityRepository;
    private Staff $model;

    public function __construct()
    {
        $this->identityRepository = new IdentityRepository;
        $this->model = new Staff;
        $this->invitationService = new InvitationService;
    }

    public function getAll($paginate = true): mixed
    {
        $query = Staff::query()->with($this->model::relations())->filter()
            ->when(request('center_id'), function ($q) {
                $centerId = request('center_id');
                if ($centerId && $centerId != 'null') {
                    $q->where('center_id', $centerId);
                }
            });

        $query = $this->identityRepository->filterIdentity($query);

        return $paginate ? $query->paginate(request('per_page', '15'))
            : $query->get();
    }

    public function createStaff($data)
    {
        DB::beginTransaction();
        try {
            [$status, $invitation] = $this->invitationService->store(['npi' => $data['npi']], $data['roles']);
            $staff = null;
            if ($status) {
                $staff = Staff::query()->create([
                    'position_id' => $data['position_id'],
                    "invitation_id" => $invitation->id,
                    "center_id" => $data['center_id'],
                ]);
                $staff->organizations()->attach($data['organizations']);
            }

            DB::commit();
            return $staff;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }


    public function getStaffById($staffId): Model|Builder|null
    {
        return $this->model->newQuery()
            ->with($this->model::relations())
            ->where('id', $staffId)
            ->first();
    }

    public function search(): Collection
    {
        $query = strtolower(trim(request()->input('query', '')));
        return Staff::query()
            ->with(
                [
                    'identity:id,lastname,firstname,telephone,email',
                    'agency',
                    'position',
                    'loanAgentCategory',
                ]
            )
            ->whereHas('identity', function ($builder) use ($query) {
                $builder->whereRaw("CONCAT(LOWER(firstname), ' ',LOWER(lastname)) LIKE ?", ["%$query%"])
                    ->orWhereRaw("CONCAT(LOWER(lastname), ' ',LOWER(firstname)) LIKE ?", ["%$query%"]);
            })
            ->select('id', 'identity_id', 'is_operator', 'status')
            ->get();
    }


    public function updateStatus(Staff $staff, array $data): Staff
    {
        $staff->update(['status' => $data['status']]);

        return $staff->load($staff::relations());
    }

    /**
     * @throws SecureDeleteException
     */
    public function delete(Staff $staff)
    {
        return $staff->secureDelete($staff::secureDeleteRelations());
    }

    public function updateStaffService($service, $staffId): void
    {
        $this->model
            ->newQuery()
            ->whereHas('identity.user', fn($query) =>  $query->where('id', $staffId))
            ->update(['head_organization_id' => $service->id]);
    }
    public function updateStaffCenter(array $data): Builder|Model
    {
        $staff = $this->model
            ->newQuery()
            ->whereHas('profile', fn($query) =>  $query->where('id', $data['profile_id']))
            ->first();
        $staff->update(['center_id' => $data['center_id']]);

        return $staff->refresh();
    }

    public function updateStaffOrganization(array $data): Builder|Model
    {
        $staff = $this->model
            ->newQuery()
            ->whereHas('profile', fn($query) =>  $query->where('id', $data['profile_id']))
            ->first();
        $staff->organizations()->sync($data['organizations']);

        return $staff->refresh()->load(['organizations']);
    }
}
