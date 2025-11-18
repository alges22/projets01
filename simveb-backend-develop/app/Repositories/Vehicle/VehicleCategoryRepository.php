<?php

namespace App\Repositories\Vehicle;

use App\Models\Vehicle\VehicleCategory;
use Illuminate\Support\Str;

class VehicleCategoryRepository
{
    /**
     * @param array $data
     * @return VehicleCategory
     */
    public function store(array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');

        return VehicleCategory::create($data);
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @param array $data
     * @return VehicleCategory
     */
    public function update(VehicleCategory $vehicleCategory, array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');
        $vehicleCategory->update($data);

        return $vehicleCategory;
    }
}
