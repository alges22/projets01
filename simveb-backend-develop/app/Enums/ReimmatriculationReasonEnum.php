<?php
namespace App\Enums;

Enum ReimmatriculationReasonEnum: string
{
    case RF = 'Véhicule réformé';
    case VE = 'Véhicule a subi une vente aux enchères';
    case D = 'Véhicule diplomatique';
    case OI = 'Véhicule organisation internationale ou ONG';
    case NOI = 'Véhicule normal en organisation internationale ou ONG';
    case AC = 'Véhicule dans aucun des autres cas';
}
