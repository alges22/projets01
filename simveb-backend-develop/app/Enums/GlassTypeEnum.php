<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum GlassTypeEnum: string
{

    use EnumToArray;

    case tinted = "Teinté";
    case transparent = "Transparent";

}
