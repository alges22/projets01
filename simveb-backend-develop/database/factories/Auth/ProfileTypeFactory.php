<?php

namespace Database\Factories\Auth;

use App\Models\Auth\ProfileType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auth\ProfileType>
 */
class ProfileTypeFactory extends Factory
{
    protected $model = ProfileType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word();
        return [
        'code' => $name,
        'name' => $name,
        'icon_path' => null,
        'role_id' => null
        ];
    }
}
