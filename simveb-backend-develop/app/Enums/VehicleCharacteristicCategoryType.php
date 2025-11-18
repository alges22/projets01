<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum VehicleCharacteristicCategoryType
{
    use EnumToArray;

    case string;
    case interval;
    case numeric;

}
