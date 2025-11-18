<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum LegalStatusEnum : string
{
    use EnumToArray, EnumToNameValue;

    case physical = 'Physique';
    case moral = 'Morale';


}
