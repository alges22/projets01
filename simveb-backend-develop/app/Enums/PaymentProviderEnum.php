<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum PaymentProviderEnum : string
{
    use EnumToArray;

    case fedapay = "FedaPay SAS";
    case kkiapay = "KKiapay";
}
