<?php

namespace Database\Seeders\Config;

use App\Consts\AvailableServiceType;
use App\Models\Config\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Pay attention, the order of each item is necessary
        $types = [
            [
                'code' => AvailableServiceType::IMMATRICULATION_STANDARD,
                'name' => 'Immatriculation Standard',
                'description' => "Il s’agit d’un service qui permet d’immatriculer un véhicule à quatre roues, avec l'attribution d'un numéro d’immatriculation unique.",
            ],
            [
                'code' => AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
                'name' => 'Immatriculation Prestige Label',
                'description' => "Il s’agit d’un service qui permet d’attribuer des plaques d'immatriculation spéciales à des véhicules de haute qualité, avec l'attribution d'un label unique et spécial.",
            ],
            [
                'code' => AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                'name' => 'Immatriculation Prestige Numéro',
                'description' => "Il s’agit d’un service qui permet d’attribuer des plaques d'immatriculation spéciales à des véhicules, avec l'attribution d'un numéro unique et spécial",
            ],
            [
                'code' => AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
                'name' => 'Immatriculation Prestige Numéro et Label',
                'description' => "Il s’agit d’un service qui permet d’attribuer des plaques d'immatriculation spéciales à des véhicules, avec l'attribution d'un numéro et d'un label, uniques et spéciales.",
            ],
            [
                'code' => AvailableServiceType::IMMATRICULATION,
                'name' => 'Immatriculation',
                'description' => 'Il s’agit d’un service qui permet d’immatriculer un véhicule à quatre roues, par l\'attribution d\'un numéro d’immatriculation unique',
            ],
            [
                'code' => AvailableServiceType::SALE_DECLARATION,
                'name' => 'Déclaration de vente',
                'description' => 'Il s’agit d’un service qui permet de déclarer la vente d\'un véhicule',
            ],
            [
                'code' => AvailableServiceType::MUTATION,
                'name' => 'Mutation',
                'description' => "Il s’agit d’un service qui permet le transfert de propriété d'un véhicule d'une personne à une autre, il fait généralement suite a un achat",
            ],
            [
                'code' => AvailableServiceType::VEHICLE_TRANSFORMATION,
                'name' => 'Tansformation de vehicle',
                'description' => 'Personnaliser ou adapter un véhicule à des besoins particuliers',
            ],
            [
                'code' => AvailableServiceType::TINTED_WINDOW_AUTHORIZATION,
                'name' => 'Autorisation de vitre teintée',
                'description' => 'Il s’agit du service qui permet de demander une autorisation de vitre teinté sur un véhicule.',
            ],
            [
                'code' => AvailableServiceType::GLASS_ENGRAVING,
                'name' => 'Gravage des vitres',
                'description' => 'Il s’agit du service de gravage des vitres et retroviseurs d’un véhicule.',
            ],
            [
                'code' => AvailableServiceType::TITLE_DEPOSIT,
                'name' => 'Dépôt de titre',
                'description' => 'Il s’agit d’un service qui permet soumettre des documents officiels tels que des titres de proriété ou autres',
            ],
            [
                'code' => AvailableServiceType::TITLE_RECOVERY,
                'name' => 'Reprise de titre',
                'description' => 'Il s’agit d’un service qui permet de récupérer des documents officiels tels que des titres de proriété ou autre',
            ],
            [
                'code' => AvailableServiceType::TITLE_DEPOSIT_OR_RECOVERY,
                'name' => 'Dépôt ou Reprise titre',
                'description' => 'Dépôt ou Reprise titre',
            ],
            [
                'code' => AvailableServiceType::RE_IMMATRICULATION,
                'name' => 'Réimmatriculation',
                'description' => "Il s’agit d’un service qui permet de renouveler l'enregistrement officiel d'un véhicule, avec l'attribution d'une nouvelle plaque d'immatriculation",
            ],
            [
                'code' => AvailableServiceType::PLATE_DUPLICATE,
                'name' => 'Duplicata de Plaque',
                'description' => "Il s’agit d’un service qui permet d'effectuer un duplicata de plaques",
            ],
            [
                'code' => AvailableServiceType::GRAY_CARD_DUPLICATE,
                'name' => 'Duplicata de Carte Grise',
                'description' => "Il s’agit d’un service qui permet d'effectuer un duplicata de cartes grises",
            ],
        ];

        foreach ($types as $type) {
            if ($service = ServiceType::where('name', $type['name'])->withTrashed()->first()) {
                $service->update($type);
            } else if ($service = ServiceType::where('code', $type['code'])->withTrashed()->first()) {
                $service->update($type);
            } else {
                ServiceType::create($type);
            }
        }
    }
}
