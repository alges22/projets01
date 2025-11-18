<?php

namespace Database\Factories\Immatriculation;

use App\Models\Auth\ProfileType;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Models\Vehicle\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Immatriculation\ImmatriculationFormat>
 */
class ImmatriculationFormatFactory extends Factory
{
    protected $model = ImmatriculationFormat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_category_id' => VehicleCategory::inRandomOrder()->first()->id,
            'profile_type_id' => ProfileType::inRandomOrder()->first()->id,
            'format' => []
        ];
    }
}
