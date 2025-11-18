<?php

namespace Ntech\UserPackage\Repositories;

use App\Models\Account\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserRepository
{


    function create($data): Model|bool|Builder
    {
        DB::beginTransaction();
        try {
            $user = User::query()->create($data);

            DB::commit();
            return $user;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,$exception->getMessage());
        }
    }

    /**
     * @param $userId
     * @return Collection|Builder|Builder[]|Model
     */
    public function disableOrActivateUser($userId) : Builder|array|Collection|Model
    {
        $user = $this->getUserById($userId);
        $user->update(['disabled_at' => $user->disabled_at == null? now() : null]);

        return $user->refresh();
    }

    function getUserById($userId)
    {
        return User::query()->findOrFail($userId);
    }

    function update($user,$data)
    {
        DB::beginTransaction();

        try
        {
            $user->update($data);

            DB::commit();
            return $user;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            return false;
        }
    }


    public function getByEmail($email)
    {
        return User::query()->where("email",$email)->first();
    }
}
