<?php

namespace Database\Seeders;

use App\Enums\ProfileTypesEnum;
use App\Models\Auth\ProfileType;
use App\Models\Plate\PlateColor;
use Illuminate\Database\Seeder;
use Ntech\UserPackage\Database\Seeders\Modules\ProfilesModule;

class ProfileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ProfileTypesEnum::toNameValue() as $name => $value) {
            $profileType = ProfileType::updateOrCreate(
                ['code' => $name],
                ['name' => $value],
            );

            if (in_array($name, [ProfileTypesEnum::user->name, ProfileTypesEnum::company->name])) {
                $plateColorsId = PlateColor::whereIn('name', ['creme'])->pluck('id')->toArray();

                foreach ($plateColorsId as $colorId) {
                    if ($profileType->plateColors()->where('plate_colors.id', $colorId)->doesntExist()) {
                        $profileType->plateColors()->attach($colorId);
                    }
                }
            }

            if (isset(ProfilesModule::ROLES[$name])){
                $profileType->assignRole(ProfilesModule::ROLES[$name]);
            }
        }
    }
}
