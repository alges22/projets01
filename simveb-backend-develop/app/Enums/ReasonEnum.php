<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum ReasonEnum : string
{
    use EnumToArray;

    case mutated = "Vente";
    case immatriculated = "Achat";
    case inputed = "Saisie";
}
