<?php

namespace App\Repositories\Vehicle;

use App\Models\Auth\Profile;
use App\Models\Config\Country;
use App\Models\Config\LegalStatus;
use App\Models\Config\OwnerType;
use App\Models\Config\State;
use App\Models\Config\Town;
use App\Models\Vehicle\VehicleOwner;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VehicleOwnerRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(VehicleOwner::class);
    }

    public function create()
    {
        return [
            'vehicle_owner_types' => OwnerType::query()->select(['label', 'id'])->get(),
            'legal_statuses' => LegalStatus::query()->select(['name', 'id'])->get(),
            'countries' => Country::query()->select(['id', 'name', 'numeric_code', 'iso3', 'iso2'])->get(),
            'states' => State::query()->select(['id', 'name', 'code'])->get(),
            'towns' => Town::query()->select(['id', 'name', 'state_id'])->get(),
        ];
    }

    public function getOwnerByEmail($email)
    {
        return $this->model->newQuery()->whereHas(
            'identity',
            fn($query) => $query->where('email', $email)
        )->firstOrFail();
    }

    public function getClientVehicles()
    {
        $condition = [];
        $query = $this->model->newQuery()
            ->select(['id', 'bfu', 'profile_id', 'legal_status', 'institution_id', 'created_at', 'identity_id'])
            ->with(['vehicles' => function ($query) {
                $query->where('is_transformed', false);
            }]);

        if (request()->isNotFilled('key')) {
            if (getOnlineProfile()->isUserProfile()) {
                $condition = ['profile_id' => getOnlineProfile()->id];
            } else {
                $condition = ['institution_id' => getOnlineProfile()->institution_id];
            }
            $query = $query->where($condition);
        } else {
            $query = $query->whereHas('identity', function ($query) {
                $query->where('npi', request()->get('key'))
                    ->orWhere('ifu', request()->get('key'));
            });
        }

        return $query->get();
    }

    public function getOwner(array $npiAndIfu, ?string $institutionId, Profile $profile = null): Model|Builder|null
    {
        if (isset($npiAndIfu['npi'])) {
            return $this->model->newQuery()
                ->orWhereHas('identity', fn($query) => $query->where('npi', $npiAndIfu['npi']))
                ->orWhere('institution_id', $institutionId)
                ->first();
        } else {
            return $this->model->newQuery()
                ->whereHas('institution', function ($q) use ($npiAndIfu, $institutionId) {
                    $q->where('ifu', $npiAndIfu['ifu'])
                        ->when($institutionId, function ($q) use ($institutionId) {
                            $q->where('institutions.id', $institutionId);
                        });
                })
                ->when($profile, function ($q) use ($profile) {
                    $q->where('profile_id', $profile->id);
                })
                ->first();
        }
    }
    public function getOwnerBySpace(string $spaceId): Model|Builder|null
    {
        return $this->model->newQuery()->where('space_id', $spaceId)->first();
    }

    public function duplicateDemands(VehicleOwner $vehicleOwner)
    {
        return [
            //PlateDuplicate::where('vehicle_owner_id', $vehicleOwner->id)->get(),
            //GrayCardDuplicate::where('vehicle_owner_id', $vehicleOwner->id)->get(),
        ];
    }
}
