<?php

namespace App\Repositories\Immatriculation;

use App\Models\Auth\ProfileType;
use App\Models\Immatriculation\FormatComponent;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Models\Vehicle\VehicleCategory;
use App\Repositories\Crud\AbstractCrudRepository;

class ImmatriculationFormatRepository extends AbstractCrudRepository
{

    public function __construct()
    {
        parent::__construct(ImmatriculationFormat::class);
    }

    public function create()
    {
        return [
            'components' => FormatComponent::query()->select(['code','description','possible_values','id'])->get(),
            'vehicle_categories' => VehicleCategory::query()->select(['label','id'])->get(),
            'profile_types' => ProfileType::query()->select(['name','id'])->get(),
        ];
    }

    public function getFormatByVehicleCategory($categoryId)
    {
        return $this->model->newQuery()->where('vehicle_category_id',$categoryId)->first();
    }


    public function getFormatByVehicleCategoryAndProfile($categoryId, $profileTypeId)
    {
        return $this->model->newQuery()
        ->where('profile_type_id',$profileTypeId)
        ->where('vehicle_category_id',$categoryId)
        ->first();
    }

    public function getFormatByProfileType(string $profileTypeId)
    {
        return $this->model->newQuery()->where('profile_type_id',$profileTypeId)->first();
    }
}
