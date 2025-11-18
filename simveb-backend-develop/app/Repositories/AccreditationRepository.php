<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Models\Account\User;
use App\Models\Accreditation;
use App\Models\Auth\Profile;
use App\Repositories\Crud\AbstractCrudRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Module;
use Ntech\UserPackage\Repositories\AuthRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AccreditationRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(Accreditation::class);
    }

    /**
     *
     */
    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        return $this->model->newQuery()
            ->select(['id', 'receiver_id', 'author_id', 'status', 'created_at'])
            ->with([
                'receiver:id,identity_id,type_id' => [
                    'identity:id,firstname,lastname,npi',
                    'type:id,name'
                ],
                'author:id,identity_id' => ['identity:id,firstname,lastname']
            ])
            ->orderByDesc('created_at')
            ->filter()
            ->paginate();
    }

    /**
     *
     */
    public function getUserProfiles(array $data)
    {
        $user = User::select(['id', 'username', 'identity_id'])->where('username', $data['npi'])->first();

        return $user->load([
            'identity:id,firstname,lastname',
            'profiles:id,user_id,institution_id,type_id',
            'profiles.type:id,name,code',
            'profiles.institution:id,name'
        ]);
    }

    /**
     *
     */
    public function getPendings(): mixed
    {
        return $this->model->newQuery()
            ->select(['id', 'receiver_id', 'authored_by', 'created_at'])
            ->where('status', Status::pending->name)
            ->with([
                'receiver:id,identity_id,type_id' => [
                    'type:id,name',
                    'identity:id,firstname,lastname,npi',
                ],
                'author:id,identity_id' => ['identity:id,firstname,lastname']
            ])
            ->paginate();
    }

    /**
     *
     */
    public function create(array $data): array
    {
        $profile = Profile::find($data['profile_id']);
        $profileTypeRoles = $profile->type->roles();
        $roles = $profileTypeRoles->whereNotIn('id', $profile->roles()->pluck('id')->toArray())->get();

        $permissions = [];
        foreach ($profileTypeRoles->get() as $role) {
            $permissions = array_merge(
                $permissions,
                $role->permissions()
                    ->whereNotIn('name', (new AuthRepository)->getProfilePermissions($profile))
                    ->pluck("id")
                    ->toArray()
            );
        }

        $modules = Module::select('id', 'name')->withWhereHas('permissions', function ($query) use ($permissions) {
            $query->select('id', 'name', 'label', 'module_id');
            $query->whereIn('id', $permissions);
        })->get();

        return [
            'roles' => $roles->select('id', 'name', 'label'),
            'modules' => $modules
        ];
    }

    /**
     *
     */
    public function store(array $data, $request = null): ?Model
    {
        DB::beginTransaction();
        try {
            $data['authored_at'] = now();
            $accreditation = parent::store($data);

            $accreditation->roles()->attach($data['roles']);
            $accreditation->permissions()->attach($data['permissions']);

            DB::commit();
            return $accreditation;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    /**
     *
     */
    public function update(Model $model, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            $accreditation = parent::update($model, $data);
            $accreditation->roles()->sync($data['roles']);
            $accreditation->permissions()->sync($data['permissions']);

            DB::commit();
            return $accreditation;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    /**
     *
     */
    public function validate(array $data): array|Model
    {
        DB::beginTransaction();
        try {
            $accreditation = $this->find($data['accreditation_id']);
            if ($accreditation->validated_at) {
                throw new HttpException(ResponseAlias::HTTP_FORBIDDEN, "Cette demande d'accréditation à déjà été validée.");
            } else {
                $data['status'] = Status::validated->name;
                $data['validated_at'] = now();

                if ($accreditation->roles->isNotEmpty()) {
                    $accreditation->receiver->assignRole($accreditation->roles);
                }
                if ($accreditation->permissions->isNotEmpty()) {
                    $accreditation->receiver->givePermissionTo($accreditation->permissions);
                }
                $model = parent::update($accreditation, $data);
            }

            DB::commit();
            return $model;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /**
     *
     */
    public function reject(array $data)
    {
        DB::beginTransaction();
        try {
            $accreditaion = $this->find($data['accreditation_id']);
            if ($accreditaion->validated_at || $accreditaion->rejected_at) {
                throw new HttpException(ResponseAlias::HTTP_FORBIDDEN, "Impossible d'éffectuer cette action.");
            } else {
                $data['status'] = Status::rejected->name;
                $data['rejected_at'] = now();
                $model = parent::update($accreditaion, $data);
            }

            DB::commit();
            return $model;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
