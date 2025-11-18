<?php

namespace Database\Seeders\Config;

use App\Consts\AvailableServiceType;
use App\Enums\ProfileTypesEnum;
use App\Models\Config\ManagementCenter;
use App\Models\Config\ManagementCenterType;
use App\Models\Config\Park;
use App\Models\Config\Service;
use App\Models\Config\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ntech\UserPackage\Models\Identity;
use Ntech\UserPackage\Models\Staff;

class ManagementCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminIdentity = Identity::where('email', 'nautilustest@mail.com')->first();

        $geographicCenterType = ManagementCenterType::whereRaw('LOWER(label) LIKE ?', ["%géo%"])->first();

        $centerData = [
            [
                'name' => 'Centre de gestion de Cotonou',
                'manager_title' => 'Superviseur',
                'state_id' => $adminIdentity->state_id,
                'town_id' => $adminIdentity->town_id,
                'district_id' => $adminIdentity->district_id,
                'village_id' => $adminIdentity->village_id,
                'staff_id' => $adminIdentity->staff->id,
                'management_center_type_id' => $geographicCenterType->id,
                'responsible_id' => $adminIdentity->user->profiles()->whereRelation('type', 'code', ProfileTypesEnum::anatt->name)->first()->id,
                'services' => Service::whereIn('code', [AvailableServiceType::IMMATRICULATION_STANDARD, AvailableServiceType::RE_IMMATRICULATION])->pluck('id')->toArray(),
                'zones' => Zone::query()->whereRelation('towns','name','like','%COTONOU%')->pluck('id')->toArray(),
                'parks' => Park::pluck('id')->toArray(),
            ],
            [
                'name' => 'Centre de gestion de Calavi',
                'manager_title' => 'Superviseur',
                'state_id' => $adminIdentity->state_id,
                'town_id' => $adminIdentity->town_id,
                'district_id' => $adminIdentity->district_id,
                'village_id' => $adminIdentity->village_id,
                'staff_id' => $adminIdentity->staff->id,
                'management_center_type_id' => $geographicCenterType->id,
                'responsible_id' => $adminIdentity->user->profiles()->whereRelation('type', 'code', ProfileTypesEnum::anatt->name)->first()->id,
                'services' => Service::whereIn('code', [
                    AvailableServiceType::IMMATRICULATION_STANDARD,
                    AvailableServiceType::RE_IMMATRICULATION,
                ])->pluck('id')->toArray(),
                'zones' => Zone::query()->whereRelation('towns','name','like','%CALAVI%')->pluck('id')->toArray(),
                'parks' => Park::pluck('id')->toArray(),
            ]
        ];

        foreach ($centerData as $data) {
            $managementCenter = ManagementCenter::updateOrCreate(
                ['name' => $data['name']],
                [
                    'manager_title' => $data['manager_title'],
                    'state_id' => $data['state_id'],
                    'town_id' => $data['town_id'],
                    'district_id' => $data['district_id'] ?? null,
                    'village_id' => $data['village_id'] ?? null,
                    'staff_id' => $data['staff_id'] ?? null,
                    'management_center_type_id' => $data['management_center_type_id'],
                    'responsible_id' => $data['responsible_id'] ?? null,
                ]
            );

            $managementCenter->services()->sync($data['services'] ?? []);
            $managementCenter->zones()->sync($data['zones'] ?? []);
            $managementCenter->parks()->sync($data['parks'] ?? []);
        }

        Staff::query()->update(['center_id' => ManagementCenter::first()?->id]);
    }
}
