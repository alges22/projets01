<?php

namespace Database\Seeders;

use App\Models\Config\Service;
use App\Models\Duplicate\GrayCardDuplicate;
use App\Models\GrayCard;
use App\Repositories\Duplicate\DuplicateGrayCardRepository;
use Illuminate\Database\Seeder;

class GrayCardDuplicateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $repository = new DuplicateGrayCardRepository();
        $grayCard = GrayCard::query()->latest()->first();

        $demand = $repository->store([
            'number' => time(),
            'gray_card_id' => $grayCard->id,
            'vehicle_owner_id' => $grayCard->vehicle_owner_id,
            'is_spoiled' => true,
            'service_id' => Service::query()->first()->id,
            'payment_status' => 'approved',
            'reference' => 'SEED-DUPLICATE-'.time()
        ]);

        $this->command->info("gray card duplicate created : $demand->id");

    }
}
