<?php

namespace Database\Factories\Config;

use App\Enums\Status;
use App\Models\Config\Service;
use App\Models\Config\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Config\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;
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
            'description' => null,
            'type_id' => ServiceType::inRandomOrder()->first()->id,
            'duration' => null,
            'cost' => null,
            'procedures' => null,
            'extract' => null,
            'who_can_apply' => null,
            'link' => null,
            'status' => Status::validated->name,
            'published' => false,
            'published_at' => null,
            'published_by' => null,
            'target_organization_id' => null,
            'parent_service_id' => null,
            'vehicle_category_id' => null,
            'image' => null,
            'color' => null,
            'is_child' => false,
            'is_active' => true,
            'can_be_demanded' => true,
            'deactived_at' => null,
            'deactived_by' => null
        ];
    }
}
