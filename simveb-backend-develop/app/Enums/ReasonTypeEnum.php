<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum ReasonTypeEnum : string
{
    use EnumToArray, EnumToNameValue;

    case opposition = "Opposition";
    case title_deposit = 'Dépôt de titre';
}
