<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

enum VehiclePassageType: string
{
    use EnumToArray, EnumToNameValue;

    case in = "Entrée";
    case out = "Sortie";
}
