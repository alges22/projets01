<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum SpaceTypesEnum : string
{
    use EnumToArray, EnumToNameValue;

    case state = 'État';
    case company = 'Entreprise';

}
