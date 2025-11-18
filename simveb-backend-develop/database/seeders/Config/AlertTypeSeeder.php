<?php

namespace Database\Seeders\Config;

use Illuminate\Support\Str;
use App\Models\Alert\AlertType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AlertTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                "name" => "Visite Technique",
                "code" => "TECHNICAL_VISIT",
                "description" => "",
                "image" => [
                    "path" => "storage/images/image.png",
                    "name" => Str::random(10).".png",
                    "type" => "image"
                ],
            ],
            [
                "name" => "Assurance",
                "code" => "INSURANCE",
                "description" => "",
                "image" => [
                    "path" => "storage/images/image.png",
                    "name" => Str::random(10).".png",
                    "type" => "image"
                ],
            ],
            [
                "name" => "Gage",
                "code" => "GAGE",
                "description" => "",
                "image" => [
                    "path" => "storage/images/image.png",
                    "name" => Str::random(10).".png",
                    "type" => "image"
                ],
            ],
            [
                "name" => "Opposition",
                "code" => "OPPOSITION",
                "description" => "",
                "image" => [
                    "path" => "storage/images/image.png",
                    "name" => Str::random(10).".png",
                    "type" => "image"
                ],
            ],
            [
                "name" => "Wanted",
                "code" => "WANTED",
                "description" => "",
                "image" => [
                    "path" => "storage/images/image.png",
                    "name" => Str::random(10).".png",
                    "type" => "image"
                ],
            ],
            [
                "name" => "Autre",
                "code" => "OTHER",
                "description" => "",
                "image" => [
                    "path" => "storage/images/image.png",
                    "name" => Str::random(10).".png",
                    "type" => "image"
                ],
            ],
        ];

        foreach ($types as $type) {
            AlertType::updateOrCreate(
                [
                    "name" => $type["name"],
                    "code" => $type["code"]
                ],
                [
                    'description' => $type['description'] ?? null,
                    'image' => json_encode($type['image'] ?? []),
                ]
            );
        }
    }
}
