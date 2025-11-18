<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

enum SpaceTemplateEnum: string
{

    use EnumToArray, EnumToNameValue;

    case default = "Serenity";
    case vivid = "Vivid";
    case focus = "Focus";
}
