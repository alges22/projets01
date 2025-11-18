<?php

namespace Database\Factories\Vehicle;

use App\Models\Vehicle\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\VehicleCategory>
 */
class VehicleCategoryFactory extends Factory
{
    protected $model = VehicleCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'label'=> fake()->name(),
            'nb_plate' => rand(1, 2),
            'price' => null,
        ];
    }
}
