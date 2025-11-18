<?php

namespace Database\Seeders\Config;

use App\Enums\Status;
use App\Models\Config\Step;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'label' => 'Soumis',
                'status' => Status::submitted->name,
                'on_portal' => true,
            ],
            [
                'label' => 'Affecté à un centre de gestion',
                'status' => Status::assigned_to_center->name,
            ],
            [
                'label' => 'Affecté à un service',
                'status' => Status::assigned_to_service->name,
            ],
            [
                'label' => 'Affecté à un staff',
                'status' => Status::assigned_to_staff->name,
            ],
            [
                'label' => 'Vérification',
                'status' => Status::verified->name,
            ],
            [
                'label' => 'Interpole',
                'status' => Status::affected_to_interpol->name,
                'on_portal' => true,
            ],
            [
                'label' => 'Pré-validation',
                'status' => Status::pre_validated->name,
            ],
            [
                'label' => 'Validation',
                'status' => Status::validated->name,
                'on_portal' => true,
            ],
            [
                'label' => "Ordre d'impression émis",
                'status' => Status::print_order_emitted->name,
                'on_portal' => false,
            ],
            [
                'label' => 'Imprimé',
                'status' => Status::print_order_validated->name,
                'on_portal' => true,
            ],
            [
                'label' => 'Cloture',
                'status' => Status::closed->name,
                'on_portal' => true,
            ],
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            Step::updateOrCreate(
                [
                    'label' => $data['label'],
                ],
                [
                    'status' => $data['status'],
                    'on_portal' => $data['on_portal'] ?? false,
                ]
            );
        }
    }
}
