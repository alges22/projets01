<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

enum VehicleTypeAtBorder: string
{
    use EnumToNameValue, EnumToArray;

    case local = "Local";
    case external = "Étranger";

}
