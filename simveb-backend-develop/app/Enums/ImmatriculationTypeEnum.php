<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

enum ImmatriculationTypeEnum: string
{
    use EnumToNameValue, EnumToArray;

    case gov = "Immatriculation Gouvernementale";
    case diplomatic = "Immatriculation Diplomatique";
    case mai = "Immatriculation des Organisation Internationale";
    case common = "Immatriculation Ordinaire";

}
