<?php

namespace App\Repositories\Vehicle;

use App\Enums\VehicleCharacteristicCategoryType;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;

class VehicleCharacteristicRepository
{
    /**
     * @return array
     */
    public function getCreateData(): array
    {
        return [
            'categories' => VehicleCharacteristicCategory::get(['id', 'name', 'label', 'type']),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function store(array $data)
    {
        $category = VehicleCharacteristicCategory::findOrFail($data['category_id']);

        if ($this->checkIfCharacteristicExists($category, $data)) {
            return [false, ["message" => "La caractéristique que vous voulez enregistré existe déjà."]];
        }

        return [true, VehicleCharacteristic::create($data)];
    }

    /**
     * @param VehicleCharacteristic $vehicleCharacteristic
     * @param array $data
     * @return array
     */
    public function update(VehicleCharacteristic $vehicleCharacteristic, array $data)
    {
        $category = VehicleCharacteristicCategory::findOrFail($data['category_id']);

        if ($this->checkIfCharacteristicExists($category, $data, $vehicleCharacteristic->id)) {
            return [false, ["message" => "La caractéristique que vous voulez enregistré existe déjà."]];
        }

        $vehicleCharacteristic->update($data);

        return [true, $vehicleCharacteristic];
    }

    /**
     * @param VehicleCharacteristicCategory $category
     * @param ?string $ignoreId
     * @return bool
     */
    public function checkIfCharacteristicExists(VehicleCharacteristicCategory $category, array $data, string $ignoreId = null): bool
    {
        return VehicleCharacteristic::where('category_id', $category->id)
            ->when($category->type != VehicleCharacteristicCategoryType::interval->name, function ($q) use ($data) {
                $q->where('value', $data['value']);
            })
            ->when($category->type == VehicleCharacteristicCategoryType::interval->name, function ($q) use ($data) {
                $q->where('min_value', $data['min_value'])
                    ->where('max_value', $data['max_value']);
            })
            ->where('id', '!=', $ignoreId)
            ->exists();
    }
}
