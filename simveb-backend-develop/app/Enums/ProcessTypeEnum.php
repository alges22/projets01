<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum ProcessTypeEnum : string
{
    use EnumToArray;

    case automatic = "Automatique";
    case manual = "Mannuel";

}
