<?php

namespace App\Repositories;

use App\Enums\ProfileTypesEnum;
use App\Http\Resources\ProfileResource;
use App\Models\Auth\Profile;
use App\Models\Config\Service;
use App\Models\Config\District;
use App\Models\Config\ManagementCenter;
use App\Models\Config\ManagementCenterType;
use App\Models\Config\OwnerType;
use App\Models\Config\Park;
use App\Models\Config\State;
use App\Models\Config\Town;
use App\Models\Config\Village;
use App\Models\Config\Zone;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Model;
use Ntech\UserPackage\Models\Staff;

class ManagementCenterRepository extends AbstractCrudRepository
{
    public function __construct()
    {
      parent::__construct(ManagementCenter::class);
    }

    public function create()
    {
        return [
            'states' => State::query()->select(['id', 'name'])->get(),
            'profiles' => ProfileResource::collection(Profile::query()->whereHas('type', fn($query) => $query->where('code', ProfileTypesEnum::anatt->name))->get()),
            'management_center_types' => ManagementCenterType::query()->select(['id', 'label'])->get(),
            'services' => Service::query()->select(['id', 'name'])->get(),
            'zones' => Zone::query()->select(['id', 'name'])->get(),
            'parks' => Park::query()->select(['id', 'name'])->get(),
        ];
    }

    public function store(array $data, $request = null): Model|null
    {
        $managementCenter = parent::store($data, $request);
        $managementCenter->services()->sync($request->services);
        $managementCenter->zones()->sync($request->zones);
        $managementCenter->parks()->sync($request->parks);

        return $managementCenter;
    }

    public function update(Model $model, array $data, $request = null): Model
    {
        $managementCenter = parent::update($model, $data, $request);
        $managementCenter->services()->sync($request->services);
        $managementCenter->zones()->sync($request->zones);
        $managementCenter->parks()->sync($request->parks);

        return $managementCenter;
    }
}
