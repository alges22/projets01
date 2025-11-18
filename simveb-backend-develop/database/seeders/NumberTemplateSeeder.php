<?php

namespace Database\Seeders;

use App\Models\Auth\Profile;
use App\Models\Config\NumberTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NumberTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            'XXYY',
            'XYYX',
            'XYXY',
        ];

        foreach ($templates as $template){
            NumberTemplate::query()->updateOrCreate(['template' => $template],['template' => $template, 'author_id' => Profile::query()->first()->id]);
        }
    }
}
