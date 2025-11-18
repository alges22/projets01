<?php

namespace Database\Seeders;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Models\Space\Space;
use App\Models\Auth\ProfileType;
use App\Enums\ProfileTypesEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function League\Uri\UriTemplate\first;

class SpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileTypes = ProfileType::query()->get();

        foreach ($profileTypes as $profileType) {
            if ($profileType->code !== ProfileTypesEnum::court->name) {
                Space::updateOrCreate([
                    'profile_type_id' => $profileType->id,
                ],[
                    'profile_type_id' => $profileType->id,
                ]);
            }
        }
    }
}
