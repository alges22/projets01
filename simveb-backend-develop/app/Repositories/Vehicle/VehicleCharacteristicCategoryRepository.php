<?php

namespace App\Repositories\Vehicle;

use App\Enums\VehicleCharacteristicCategoryType;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VehicleCharacteristicCategoryRepository
{
    /**
     * @return array
     */
    public function getCreateData(): array
    {
        foreach (VehicleCharacteristicCategoryType::toArray() as $name) {
            $types[] = [
                'value' => $name,
                'label' => __('vehicle-characteristic-category-type.' . $name),
            ];
        }

        return [
            'types' => $types,
        ];
    }

    /**
     * @param array $data
     * @return VehicleCharacteristicCategory
     */
    public function store(array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');

        return VehicleCharacteristicCategory::create($data);
    }

    /**
     * @param VehicleCharacteristicCategory $vehicleCharacteristicCategory
     * @param array $data
     * @return VehicleCharacteristicCategory
     */
    public function update(VehicleCharacteristicCategory $vehicleCharacteristicCategory, array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');
        $vehicleCharacteristicCategory->update($data);

        return $vehicleCharacteristicCategory;
    }

    public function updateFieldNames(array $data)
    {
        DB::beginTransaction();
        try {
            DB::commit();

            $categories = collect();

            foreach ($data['fields'] as  $fieldData) {
                if ($category = VehicleCharacteristicCategory::where('field_name', $fieldData['field_name'])->first()) {
                    $category->update(['field_name' => null]);
                }
                $category = VehicleCharacteristicCategory::find($fieldData['category_id']);

                $category->update(['field_name' => $fieldData['field_name']]);

                $categories->push($category);
            }

            DB::commit();

            return $categories;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(500, $exception->getMessage());
        }
    }
}
