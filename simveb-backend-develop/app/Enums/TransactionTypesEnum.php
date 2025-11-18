<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum TransactionTypesEnum : string
{
    use EnumToArray, EnumToNameValue;

    case debit = 'Débit';
    case credit = 'Crédit';
}
