<?php

namespace App\Repositories;

use App\Models\Account\Declarant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Repositories\IdentityRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class DeclarantRepository
{
    private IdentityRepository $identityRepository;
    private Declarant $model;

    public function __construct()
    {
        $this->identityRepository = new IdentityRepository;
        $this->model = new Declarant;
    }

    public function getAll($paginate = true): mixed
    {
        $query = Declarant::query()->with($this->model::relations())->filter();
        $query = $this->identityRepository->filterIdentity($query);

        return $paginate ? $query->paginate(request('per_page', '15')) : $query->get();
    }

    public function createDeclarant($data): Model|Builder|bool
    {
        DB::beginTransaction();
        try {
            $userId = Auth::user()->id;

            $identity = $this->identityRepository->create([
                'email' => $data['email'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'telephone' => $data['telephone'],
                'user_id' => $userId ? $userId : "null",
            ]);

            $declarant = Declarant::query()->create([
                'identity_id' => $identity->id,
                'institution_id' => $data['institution_id']
            ]);

            DB::commit();
            return $declarant->load($declarant::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function updateDeclarant($data, Declarant $declarant): Model|Builder|bool
    {
        DB::beginTransaction();

        try {

            $this->identityRepository->update($declarant->identity, [
                'email' => $data['email'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'telephone' => $data['telephone'],
            ]);

            $declarant->update([
                'institution_id' => $data['institution_id']
            ]);

            DB::commit();
            $declarant->refresh();

            return $declarant->load($declarant::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function showDeclarant($declarantId)
    {
        return $this->model->newQuery()
            ->with($this->model::relations())
            ->where('id', $declarantId)
            ->first();
    }

    public function deleteDeclarant(Declarant $declarant)
    {
        $declarant->secureDelete($declarant::secureDeleteRelations());

        return $declarant->refresh();
    }
}
