<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Models\Config\Border;
use App\Models\PoliceOfficer\PoliceOfficer;
use App\Models\PoliceOfficer\PoliceOfficerAssignment;
use App\Repositories\Auth\ProfileRepository;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Repositories\Crud\CrudRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PoliceOfficerAssignmentRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(PoliceOfficerAssignment::class);
    }

    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        return $this->model
            ->newQuery()
            ->select(['id', 'profile_id', 'border_id', 'status', 'created_at'])
            ->with([
                'border:id,name',
                'profile:id,user_id,identity_id,type_id' => ['identity:id,firstname,lastname,email,npi']
            ])
            ->orderByDesc('created_at')
            ->filter()
            ->paginate(request('per_page', '15'));
    }

    /**
     * @return array
     */
    public function create(): array
    {
        return [
            'borders' => Border::select(['id', 'name'])->get()
        ];
    }

    /**
     * @param array $data
     * @param $request
     * @return Model|null
     */
    public function store(array $data, $request = null): ?Model
    {
        DB::beginTransaction();
        try {
            $data['authored_at'] = now();
            $assignment = parent::store($data);
            DB::commit();
            return $assignment;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    /**
     * @param array $data
     * @return Model
     */
    public function validate(array $data): Model
    {
        DB::beginTransaction();

        try {
            $assignment = $this->find($data['assignment_id']);
            $policeOfficerRepository = new CrudRepository(PoliceOfficer::class);

            if ($officerProfile = (new ProfileRepository)->find($assignment->profile_id)) {
                if ($assignment->status === Status::validated->name) {
                    throw new HttpException(ResponseAlias::HTTP_CONFLICT, "Cette demande d'affectation à déjà été validée.");
                }
                if ($assignment->border_id === $policeOfficerRepository->findWhere(['profile_id' => $officerProfile->id])?->border_id) {
                    throw new HttpException(ResponseAlias::HTTP_CONFLICT, 'Ce profile est déjà affecté à cette frontière.');
                }
                $policeOfficerRepository->storeOrUpdate([
                    'border_id' => $assignment->border_id,
                ], [
                    'profile_id' => $officerProfile->id,
                    'identity_id' => $officerProfile->identity_id,
                ]);
                unset($data['assignment_id']);
                $data['validated_at'] = now();
                $data['status'] = Status::validated->name;
                $validatedAssignment = parent::update($assignment, $data);
            }

            DB::commit();
            return $validatedAssignment;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /**
     * @param array $data
     * @return Model
     */
    public function reject(array $data): Model
    {
        DB::beginTransaction();

        try {
            $assignment = $this->find($data['assignment_id']);

            unset($data['assignment_id']);
            $data['rejected_at'] = now();
            $data['status'] = Status::rejected->name;
            $rejectedAssignment = parent::update($assignment, $data);

            DB::commit();
            return $rejectedAssignment;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
