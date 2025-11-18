<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use App\Traits\EnumToNameValue;

Enum DemandUpdatesTypeEnum : string
{
    use EnumToArray, EnumToNameValue;

    case authorization_id = "Autorisation";
	case back_plate_shape_id = "ID de forme Plaque Arrière";
	case back_plate_shape = "Forme Plaque Arrière";
	case bfu = "Bordereau de Frais Unique";
	case cni = "Carte Nationale d'Identité";
	case comment = "Commentaire";
    case attachments = "Pièce jointes";
	case custom_reason = "Raison Spécifique";
    case declaration_of_law = "Déclaration de loi";
	case deposit_id = "Dépôt";
	case desired_number = "Numéro Désiré";
	case driver_lience = "Permis de conduire";
	case front_plate_shape_id = "ID de forme Plaque Avant";
	case front_plate_shape = "Forme Plaque Avant";
	case gray_card_id = "Carte Grise";
	case institution_id = "Institution";
	case is_lost = "Perte";
	case is_spoiled = "Endomagé";
	case label = "Label";
	case new_owner_ifu = "IFU du Nouveau Propriétaire";
	case new_owner_npi = "NPI du Nouveau Propriétaire";
	case old_plate_id = "Ancienne Plaque";
	case passport = "Passeport";
	case plate_color_id = "ID de couleur de Plaque";
	case plate_color = "Couleur de Plaque";
	case profile_id = "Profile";
	case rccm = "Registre du Commerce et du Crédit Mobilier";
	case reason = "Raison";
	case sale_declaration_reference = "Référence Déclaration de Vente";
	case service_id = "Service";
	case title_reason = "Raison [Dépôt/Reprise] de Titre";
	case vehicle_owner_id = "Propriétaire du Véhicule";
	case vehicle_photo = "Photo du Véhicule";
	case with_immatriculation = "Avec Immatriculation";
	case vehicle_transformation_type = "Type de transformation de véhicule";
	case vehicle_characteristic_category = "Categorie de la caractéristique du véhicule";
	case vehicle_characteristic = "Caractéristique du véhicule";
}
