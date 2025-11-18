<?php

namespace Database\Seeders\Config;

use App\Enums\PaymentProviderEnum;
use App\Models\Auth\Profile;
use App\Models\Config\PaymentProvider;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'code' => PaymentProviderEnum::fedapay->name,
                'is_default' => true,
                'is_active' => true,
                'activator_id' => Profile::first()->id,
                'author_id' => Profile::first()->id,
            ],
            [
                'code' => PaymentProviderEnum::kkiapay->name,
                'is_default' => false,
                'is_active' => true,
                'activator_id' => Profile::first()->id,
                'author_id' => Profile::first()->id,
            ],
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            PaymentProvider::updateOrCreate(
                [
                    'code' => $data['code'],
                ],[
                    'is_default' => $data['is_default'],
                    'is_active' => $data['is_active'],
                    'activator_id' => $data['activator_id'],
                    'author_id' => $data['author_id'],
                    'activated_at' => Carbon::now(),
                ]
            );
        }
    }
}
