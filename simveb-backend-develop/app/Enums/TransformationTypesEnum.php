<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum TransformationTypesEnum : string
{
    use EnumToArray;

    case aesthetics = "Esthétique";
    case performance = "Performance";

}
