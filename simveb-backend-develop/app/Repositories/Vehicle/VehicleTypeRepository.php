<?php

namespace App\Repositories\Vehicle;

use App\Models\Vehicle\VehicleType;
use Illuminate\Support\Str;

class VehicleTypeRepository
{
    /**
     * @param array $data
     * @return VehicleType
     */
    public function store(array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');

        return VehicleType::create($data);
    }

    /**
     * @param VehicleType $vehicleType
     * @param array $data
     * @return VehicleType
     */
    public function update(VehicleType $vehicleType, array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');
        $vehicleType->update($data);

        return $vehicleType;
    }
}
