<?php
namespace App\Enums;

use App\Traits\EnumToArray;

Enum CharacteristicCategoriesEnum: string
{
    use EnumToArray;

    case vehicle_energy = "Source d'energie";
    case glass_type = "Type de vitre";
    case rim = "Jante";
    case engine_power = "Puissance du moteur";
    case body_type = "Type de carrosserie";
    case vehicle_model = "Modèle du véhicule";
    case number_of_seats = "Nombre de place assise";
    case empty_weight = "Poids à vide";
    case charged_weight = "Poids à charge";
    case horsepower = "Nombre de chevaux";
    case paint = "Peinture";
    case bodyshop = "Carrosserie";
    case color_1 = "Couleur 1";
    case color_2 = "Couleur 2";
    case color_3 = "Couleur 3";
    case color_4 = "Couleur 4";
}
