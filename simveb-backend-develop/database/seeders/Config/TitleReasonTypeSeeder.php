<?php

namespace Database\Seeders\Config;

use App\Models\Config\TitleReasonType;
use App\Enums\ReasonTypeEnum;
use Illuminate\Database\Seeder;

class TitleReasonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ReasonTypeEnum::toNameValue() as $name => $value) {
            TitleReasonType::updateOrCreate(
                ['name' => $name],
                ['description' => $value]
            );
        }
    }
}
