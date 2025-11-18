<?php

namespace App\Repositories\Auth;

use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ProfileRepository extends AbstractCrudRepository
{
    public function __construct()
    {
      parent::__construct(Profile::class);
    }

    /**
     *
     */
    public function getAll(bool $paginate = true, $relations = []): mixed
    {
        return $this->model->newQuery()
                ->select([
                    'id',
                    'identity_id',
                    'institution_id',
                    'type_id',
                    'created_at'
                ])
                ->where('user_id', getOnlineProfile()->user_id)
                ->with([
                    'identity:id,firstname,lastname,npi',
                    'institution:id,name',
                    'type:id,name'
                ])
                ->orderByDesc('created_at')
                ->filter()
                ->paginate();
    }

    /**
     *
     */
    public function view(Profile $profile)
    {
        $profileWithDemandsTotal = $this->model->newQuery()->withCount('demands')->find($profile->id);

        return $profileWithDemandsTotal->load(['actions' => fn ($query) => $query->latest()->take(10)]);
    }

    public function updateProfile(Profile $profile, $data)
    {

      $profile->identity->update($data);

      return $profile;
    }

    /**
     *
     */
    public function getTotalProfilesByTypes()
    {
        return ProfileType::query()->select(['name', 'code'])->withCount('profiles')->orderByDesc('profiles_count')->get();
    }
}
