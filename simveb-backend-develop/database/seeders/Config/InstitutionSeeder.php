<?php

namespace Database\Seeders\Config;

use App\Models\Auth\ProfileType;
use App\Models\Space\Space;
use App\Enums\ProfileTypesEnum;
use App\Models\Config\District;
use Illuminate\Database\Seeder;
use App\Enums\InstitutionTypesEnum;
use App\Models\Institution\Institution;
use App\Models\Institution\InstitutionType;

class InstitutionSeeder extends Seeder
{
    public function getData()
    {
        $district = District::where('code', 'COT12EM')->first();
        $village = $district->villages()->inRandomOrder()->first();
        $town = $district->town;

        $data = [
            [
                'name' => 'Agence Nationale des Transports Terrestre',
                'email' => 'anatt@example.com',
                'acronym' => 'ANaTT',
                'telephone' => '+22952000001',
                'address' => 'Rue 108 - Tokpa Hoho Lot 35 B - Immeuble KOUGBLENOU',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::gov_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::anatt->name,
            ],
            [
                'name' => 'INTERPOL Bureau Central National (BCN)',
                'email' => 'interpol@exemple.com',
                'acronym' => 'Interpol',
                'telephone' => '+22952000002',
                'address' => 'Cadjèhoun, von de la DEC',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::gov_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::interpol->name,
            ],
            [
                // Police
                'name' => 'Direction générale de la Police républicaine',
                'email' => 'police@exemple.com',
                'acronym' => 'Police',
                'telephone' => '+22952000003',
                'address' => 'St Michel, non loin de l\'églse atholique',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::gov_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::police->name,
            ],
            [
                //Garage Central
                'name' => 'Garage centrale',
                'email' => 'centralgarage@exemple.com',
                'acronym' => 'Garage Central',
                'telephone' => '+22952000004',
                'address' => 'Fifadji, carrefour la vie',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::gov_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::central_garage->name,
            ],
            [
                // GMA
                'name' => 'Garage du ministère des affaires intérieur',
                'email' => 'user5@simveb.com',
                'acronym' => 'GMA',
                'telephone' => '+22952000005',
                'address' => 'Fifadji, carrefour la vie',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::gov_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::gma->name,
            ],
            [
                // GMA
                'name' => 'UNICEF',
                'email' => 'unicef@simveb.com',
                'acronym' => 'unicef',
                'telephone' => '+22952002005',
                'address' => 'Fifadji, carrefour agontikon',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::io->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::company->name,
            ],
            [
                // NGO
                'name' => 'Fondation VALLET',
                'email' => 'vallet@simveb.com',
                'acronym' => 'vallet',
                'telephone' => '+22952001005',
                'address' => 'Fifadji, carrefour la vie',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::ngo->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::company->name,
            ],
            [
                // GMD
                'name' => 'Garage Matériel de la diplomatie',
                'email' => 'gmd@exemple.com',
                'acronym' => 'GMD',
                'telephone' => '+22952000006',
                'address' => 'Fifadji, carrefour la vie',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::gov_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::gmd->name,
            ],
            [
                'name' => 'Ambassade du Japon en République du Bénin',
                'email' => 'japanambassy@exemple.com',
                'ifu' => '9392919098765',
                'acronym' => 'Ambassade du Japon',
                'telephone' => '97666666',
                'address' => 'Zone Résidentielle de Cotonou sis à Djomehountin, 12ème arrondissement, Cotonou, Bénin',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::embassie->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::gmd->name,
            ],
            [
                'name' => 'Bank of Africa',
                'email' => 'user1@simveb.com',
                'ifu' => '9091929394959',
                'acronym' => 'BOA',
                'telephone' => '+22952000007',
                'address' => 'Fidjrossè, En allant vers la route des pêches',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::financial_institution->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::bank->name,
            ],
            [
                'name' => 'Tribunal de Cotonou',
                'email' => 'tribunalcot@simveb.com',
                'ifu' => '2345678901234',
                'acronym' => 'TRICOT',
                'telephone' => '+22992000002',
                'address' => 'Cadjèhoun, Cotonou',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::ministry_justice->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::court->name,
            ],
            [
                'name' => 'Tribunal d\'Abomey',
                'email' => 'tribunalabo@simveb.com',
                'ifu' => '0123456789012',
                'acronym' => 'TRIABO',
                'telephone' => '+22972010102',
                'address' => 'Dota, Abomey',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::ministry_justice->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::court->name,
            ],
            [
                'name' => 'CFAO Group',
                'email' => 'cfaogroup@example.com',
                'ifu' => '9192939495969',
                'acronym' => 'CFAO',
                'telephone' => '+22952000008',
                'address' => 'Carrefour Vèdoko',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::company->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::distributor->name,
            ],
            [
                'name' => 'Affilié de SIMVEB',
                'email' => 'affiliate1@exemple.com',
                'ifu' => '9293949596979',
                'acronym' => 'Affilié',
                'telephone' => '+22952000009',
                'address' => 'Ste Rita, En face du Ceg',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::company->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::affiliate->name,
            ],
            [
                'name' => 'Agréé de SIMVEB',
                'email' => 'approved1@simveb.bj',
                'ifu' => '9293949555320',
                'acronym' => 'Agréé',
                'telephone' => '+22952000709',
                'address' => 'Ste Rita',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::company->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::approved->name,
            ],
            [
                'name' => 'Comissaire priseur',
                'email' => 'auctioneer6@simveb.bj',
                'ifu' => '9293940023120',
                'acronym' => 'COPRI',
                'telephone' => '+22952055109',
                'address' => 'Ste Rita',
                'village_id' => $village->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'type_id' => InstitutionType::where('name', InstitutionTypesEnum::company->name)->pluck('id')->first(),
                'profile_type_code' => ProfileTypesEnum::auctioneer->name,
            ],
        ];

        return $data;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getData() as $data) {
            $institution = Institution::updateOrCreate(
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ],
                [
                    'ifu' => $data['ifu'] ?? null,
                    'acronym' => $data['acronym'],
                    'telephone' => $data['telephone'],
                    'address' => $data['address'],
                    'village_id' => $data['village_id'],
                    'district_id' => $data['district_id'],
                    'town_id' => $data['town_id'],
                    'type_id' => $data['type_id'],
                    'profile_type_code' => $data['profile_type_code'],
                ]
            );

            $profileType = ProfileType::query()->where('code', ProfileTypesEnum::court->name)->first();

            if ($institution->profile_type_code === ProfileTypesEnum::court->name) {
                Space::updateOrCreate([
                    'profile_type_id' => $profileType->id,
                    'institution_id' => $institution->id,
                ],[
                    'profile_type_id' => $profileType->id,
                    'institution_id' => $institution->id,
                ]);
            }

            $space = Space::query()
                ->whereHas('profileType', fn($query) => $query->where('code', $data['profile_type_code']))
                ->first();

            if ($space && $space->institution_id === null) {
                $space->update(['institution_id' => $institution->id]);
            }
        }
    }
}
