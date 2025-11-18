<?php

namespace Database\Factories\Config;

use App\Models\Config\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Config\ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    protected $model = ServiceType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        return [
            'code' => strtoupper($name),
            'name' => $name,
            'description' => $name,
            'cost' => rand(2000, 10000)
        ];
    }
}
