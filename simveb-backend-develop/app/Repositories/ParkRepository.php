<?php

namespace App\Repositories;

use App\Models\Space\Space;
use App\Models\Config\Park;
use App\Models\Config\Town;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleType;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Model;

class ParkRepository extends AbstractCrudRepository
{

    public function __construct()
    {
        parent::__construct(Park::class);
    }

    public function create()
    {
        return [
            'space' => Space::query()->select(['id', 'institution_id'])->with(['institution:id,name']) ->get(),
            'vehicle_types' => VehicleType::query()->select(['id','label',])->get(),
            'vehicle_categories' => VehicleCategory::query()->select(['id', 'label'])->get(),
            'towns' => Town::query()->select(['id', 'name'])->get(),
        ];
    }

    /**
     * @param array $data
     * @return Park
     */

    public function store(array $data, $request = null): Model|null
    {
        $park =  parent::store($data, $request);
        $park->vehicleTypes()->attach($request->vehicle_types);
        $park->vehicleCategories()->attach($request->vehicle_categories);
        $park->towns()->attach($request->towns);

        return $park;

    }

    /**
     * @param Model $model
     * @param array $data
     * @param null $request
     * @return Park
     */

    public function update(Model $model, array $data, $request = null): Model
    {
        $park = parent::update($model, $data, $request);
        $park->vehicleTypes()->sync($request->vehicle_types);
        $park->vehicleCategories()->sync($request->vehicle_categories);
        $park->towns()->sync($request->towns);

        return $park;

    }
}


