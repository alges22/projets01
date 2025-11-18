<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum InstitutionTypesEnum : string
{
    use EnumToArray, EnumToNameValue;

    case company = "Entreprise";
    case financial_institution = "Institution Financière";
    case gov_institution = 'Institution étatique';
    case io = "Organisation Internationale";
    case ngo = "Organisation Non-Gouvernementale";
    case consulate = "Consulat";
    case embassie = "Ambassade";
    case ministry_justice = "Ministère de la Justice";
}
