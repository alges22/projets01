<?php

namespace App\Repositories\Auth;

use App\Models\Auth\Profile;
use App\Repositories\Crud\AbstractCrudRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Ntech\UserPackage\Models\Identity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProfileSearchRepository extends AbstractCrudRepository
{
    public function __construct()
    {
      parent::__construct(Profile::class);
    }

    /**
     *
     */
    public function search(array $data): mixed
    {
        try {
            if (count(array_keys($data)) !== 1) {
                throw new Exception("The request must have only one parameter passed for the search.");
            }
            return $this->model
                ->newQuery()
                ->select([
                    'id',
                    'identity_id',
                    'institution_id',
                    'type_id',
                    'created_at'
                ])
                ->with([
                    'identity:id,firstname,lastname,npi',
                    'institution:id,name',
                    'type:id,name'
                ])
                ->orderByDesc('created_at')
                ->filter()
                ->paginate();

        } catch (Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     *
     */
    private function getProfilesByUserNpi($npi) : ?Collection
    {
        $user = Identity::where('npi', $npi)->first()->user;
        return $user->profiles->transform(function ($profile) {
            $institution = $profile->institution_id ? ['name' => $profile->institution->name] : null;
            return [
                'id' => $profile->id,
                'identity' => [
                    'firstname' => $profile->identity?->firstname,
                    'lastname' => $profile->identity?->lastname,
                ],
                'institution' => $institution,
                'profile_type' => $profile->type->name,
                'created_at' => $profile->created_at,
            ];
        });
    }

    public function getProfileDemands(Profile $profile) : mixed
    {
        return $profile->demands()->select(['id','reference','created_at','service_id','model_id','model_type'])->with(['service:id,name,type_id'])->get();
    }
}
