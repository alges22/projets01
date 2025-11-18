<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum ProfileTypesEnum : string
{
    use EnumToArray, EnumToNameValue;

    case user = 'Usager/Vendeur';
    case auctioneer = 'Commissaire priseur';

    // companies
    case company = 'Entreprise';
    case distributor = 'Concessionnaire';
    case bank = 'Banque';
    case approved = 'Agréé';
    case affiliate = 'Affilié';

    // uniques
    case interpol = 'Interpol';
    case anatt = 'ANATT';

    // states
    case police = 'Police';
    case central_garage = 'Garage central';

    case gma = 'GM Affaires intérieures';

    case gmd = 'GM Diplomatie (Ministère)';

    case court = "Tribunal";

}
