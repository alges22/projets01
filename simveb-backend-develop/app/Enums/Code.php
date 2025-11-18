<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum Code : string
{
    use EnumToArray;

    case energy = "Source d'energie";
    case glass_type = "Type de vitre";
    case rim = "Jante";
    case engine_power = "Puissance du moteur";
    case color = "Suspendu";
    case body_type = "Type de carrosserie";
    case vehicle_model = "Modèle du véhicule";
    case number_of_seats = "Nombre de place assise";
    case empty_weight = "Poids à vide";
    case charged_weight = "Poids à charge";
    case horsepower = "Nombre de chevaux";
}
