<?php

namespace Database\Seeders;

use App\Enums\ReimmatriculationReasonEnum;
use App\Models\Config\ReimmatriculationReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReimmatriculationReasonSeeder extends Seeder
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            [
                'tile' => ReimmatriculationReasonEnum::RF->value,
                'code' => 'M-' . ReimmatriculationReasonEnum::RF->name,
                'requires_reason' => false,
                'requires_title_deposit' => false,
                'requires_transfer_certificate' => false,
                'enable_plate_transformation' => true,
                'img_path' => 'assets/img/reimmatriculation-reason/vehicule_reforme.svg',
            ],
            [

                'tile' => ReimmatriculationReasonEnum::VE->value,
                'code' => 'M-' . ReimmatriculationReasonEnum::VE->name,
                'requires_reason' => false,
                'requires_title_deposit' => false,
                'requires_transfer_certificate' => false,
                'enable_plate_transformation' => false,
                'img_path' => 'assets/img/reimmatriculation-reason/vehicule_enchere.svg',
            ],
            [
                'tile' => ReimmatriculationReasonEnum::D->value,
                'code' => 'M-' . ReimmatriculationReasonEnum::D->name,
                'requires_reason' => false,
                'requires_title_deposit' => true,
                'requires_transfer_certificate' => true,
                'enable_plate_transformation' => false,
                'img_path' => 'assets/img/reimmatriculation-reason/vehicule_diplomatique.svg',
            ],
            [
                'tile' => ReimmatriculationReasonEnum::OI->value,
                'code' => 'M-' . ReimmatriculationReasonEnum::OI->name,
                'requires_reason' => false,
                'requires_title_deposit' => true,
                'requires_transfer_certificate' => true,
                'enable_plate_transformation' => false,
                'img_path' => 'assets/img/reimmatriculation-reason/vehicule_organisation_internationale.svg',
            ],
            [
                'tile' => ReimmatriculationReasonEnum::NOI->value,
                'code' => 'M-' . ReimmatriculationReasonEnum::NOI->name,
                'requires_reason' => false,
                'requires_title_deposit' => true,
                'requires_transfer_certificate' => false,
                'enable_plate_transformation' => false,
                'img_path' => 'assets/img/reimmatriculation-reason/vehicule_organisation_internationale.svg',
            ],
            [
                'tile' => ReimmatriculationReasonEnum::AC->value,
                'code' => 'M-' . ReimmatriculationReasonEnum::AC->name,
                'requires_reason' => true,
                'requires_title_deposit' => false,
                'requires_transfer_certificate' => false,
                'enable_plate_transformation' => false,
                'img_path' => 'assets/img/reimmatriculation-reason/vehicule_de_particulier.svg',
            ],
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            ReimmatriculationReason::updateOrCreate([
                'code' => $data['code'],
            ], [
                'title' => $data['tile'],
                'requires_reason' => $data['requires_reason'] ?? false,
                'requires_title_deposit' => $data['requires_title_deposit'] ?? false,
                'requires_transfer_certificate' => $data['requires_transfer_certificate'] ?? false,
                'enable_plate_transformation' => $data['enable_plate_transformation'] ?? false,
                'img_path' => $data['img_path'] ?? null,
            ]);
        }
    }
}
