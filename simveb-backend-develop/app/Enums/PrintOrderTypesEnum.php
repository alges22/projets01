<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum PrintOrderTypesEnum: string
{
    use EnumToArray;

    case plate = 'Plaque';
    case gray_card = 'Carte grise';
    case both = 'Plaque et carte grise';
}
