<?php

namespace App\Repositories;

use App\Models\Config\Town;
use App\Models\Config\Zone;
use App\Repositories\Crud\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Model;

class ZoneRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(Zone::class);
    }

    public function create()
    {
        return [
          'towns' => Town::query()->select(['name','id'])->get()
        ];
    }
    public function edit(Zone $zone)
    {
        return [
            'towns' => Town::query()->select(['name','id'])->whereDoesntHave('zones')
                ->orWhereHas('zones', function ($query) use ($zone) {
                    $query->where('zones.id', $zone->id);
                })->get()

        ];
    }

    public function store(array $data, $request = null): Model
    {
        $zone = parent::store($data, $request);
        $zone->towns()->attach($request->towns);

        return $zone;
    }

    public function update(Model $model, array $data, $request = null): Model
    {
        $zone = parent::update($model, $data, $request);
        $zone->towns()->sync($request->towns);

        return $zone;
    }
}
