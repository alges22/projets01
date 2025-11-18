<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum GenderEnum: string
{

    use EnumToArray;
    case F = "Female";
    case M = "Male";
}
