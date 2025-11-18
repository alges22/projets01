<?php

namespace Ntech\UserPackage\Database\Seeders\Modules;

use App\Consts\AvailableServiceType;

class ServicesModule
{

    const GLOBAL_SERVICE = "global-service";
    const PERMISSIONS = [
        [
            "name" => "Gestion des demandes",
            "code" => self::GLOBAL_SERVICE,
            "permissions" => [
                ["name" => "assign-to-center", "label" => "Pouvoir assigner une demande à un centre de gestion"],
                ["name" => "assign-to-service", "label" => "Pouvoir assigner une demande à un service"],
                ["name" => "assign-to-staff", "label" => "Pouvoir assigner une demande à un staff"],
                ["name" => "affect-to-interpol", "label" => "Pouvoir effecter une demande à interpole"],
                ["name" => "emit-print-order", "label" => "Pouvoir émettre un ordre d'impression"],
                ["name" => "close-demand", "label" => "Pouvoir clôture une demande"],
            ]
        ],
        [
            "name" => "Immatriculation Standard",
            "code" => AvailableServiceType::IMMATRICULATION_STANDARD,
            "permissions" => [
                ["name" => "verify-imstd", "label" => "Pouvoir vérifier une demande d'imatriculation standard"],
                ["name" => "pre-validate-imstd", "label" => "Pouvoir pré-valider une demande d'imatriculation standard"],
                ["name" => "validate-imstd", "label" => "Pouvoir valider une demande d'imatriculation standard"],
                ["name" => "reject-imstd", "label" => "Pouvoir rejeter une demande d'imatriculation standard"],
                ["name" => "interpol-validate-imstd", "label" => "Pouvoir valider au nom d'interpole"],
                ["name" => "interpol-reject-imstd", "label" => "Pouvoir rejeter au nom d'interpole"],
                ["name" => "close-imstd", "label" => "Pouvoir clôturer une demande d'imatriculation standard"],
                ["name" => "archive-imstd", "label" => "Pouvoir archiver une demande d'imatriculation standard"],
                ["name" => "print-imstd-gray-card", "label" => "Pouvoir imprimer une carte grise"],
                ["name" => "print-imstd-plate", "label" => "Pouvoir imprimer une plaque"]
            ]
        ],
        [
            "name" => "Réimmatriculation",
            "code" => AvailableServiceType::RE_IMMATRICULATION,
            "permissions" => [
                ["name" => "verify-rim", "label" => "Pouvoir vérifier une demande de ré-imatriculation"],
                ["name" => "pre-validate-rim", "label" => "Pouvoir pré-valider une demande de ré-imatriculation"],
                ["name" => "validate-rim", "label" => "Pouvoir valider une demande de ré-imatriculation"],
                ["name" => "reject-rim", "label" => "Pouvoir rejeter une demande de ré-imatriculation"],
                ["name" => "interpol-validate-rim", "label" => "Pouvoir valider au nom d'interpole"],
                ["name" => "interpol-reject-rim", "label" => "Pouvoir rejeter au nom d'interpole"],
                ["name" => "close-rim", "label" => "Pouvoir clôturer une demande de ré-imatriculation"],
                ["name" => "print-rim-plate", "label" => "Pouvoir imprimer une plaque"],
                ["name" => "print-rim-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Immatriculation Prestige Label",
            "code" => AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
            "permissions" => [
                ["name" => "verify-impl", "label" => "Pouvoir vérifier une demande d'imatriculation prestige label"],
                ["name" => "pre-validate-impl", "label" => "Pouvoir pré-valider une demande d'imatriculation prestige label"],
                ["name" => "validate-impl", "label" => "Pouvoir valider une demande d'imatriculation prestige label"],
                ["name" => "reject-impl", "label" => "Pouvoir rejeter une demande d'imatriculation prestige label"],
                ["name" => "close-impl", "label" => "Pouvoir clôturer une demande d'imatriculation prestige label"],
                ["name" => "print-impl-plate", "label" => "Pouvoir imprimer une plaque"],
                ["name" => "print-impl-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Immatriculation Prestige Numéro",
            "code" => AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
            "permissions" => [
                ["name" => "verify-impn", "label" => "Pouvoir vérifier une demande d'imatriculation prestige numéro"],
                ["name" => "pre-validate-impn", "label" => "Pouvoir pré-valider une demande d'imatriculation prestige numéro"],
                ["name" => "validate-impn", "label" => "Pouvoir valider une demande d'imatriculation prestige numéro"],
                ["name" => "reject-impn", "label" => "Pouvoir rejeter une demande d'imatriculation prestige numéro"],
                ["name" => "interpol-validate-impn", "label" => "Pouvoir valider au nom d'interpole"],
                ["name" => "interpol-reject-impn", "label" => "Pouvoir rejeter au nom d'interpole"],
                ["name" => "close-impn", "label" => "Pouvoir clôturer une demande d'imatriculation prestige numéro"],
                ["name" => "print-impn-plate", "label" => "Pouvoir imprimer une plaque"],
                ["name" => "print-impn-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Immatriculation Prestige Numéro et Label",
            "code" => AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
            "permissions" => [
                ["name" => "verify-impnl", "label" => "Pouvoir vérifier une demande d'imatriculation prestige numéro et label"],
                ["name" => "pre-validate-impnl", "label" => "Pouvoir pré-valider une demande d'imatriculation prestige numéro et label"],
                ["name" => "validate-impnl", "label" => "Pouvoir valider une demande d'imatriculation prestige numéro et label"],
                ["name" => "reject-impnl", "label" => "Pouvoir rejeter une demande d'imatriculation prestige numéro et label"],
                ["name" => "close-impnl", "label" => "Pouvoir clôturer une demande d'imatriculation prestige numéro et label"],
                ["name" => "print-impnl-plate", "label" => "Pouvoir imprimer une plaque"],
                ["name" => "print-impnl-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Déclaration de vente",
            "code" => AvailableServiceType::SALE_DECLARATION,
            "permissions" => [
                ["name" => "verify-dcvt", "label" => "Pouvoir vérifier une déclaration de vente"],
                ["name" => "pre-validate-dcvt", "label" => "Pouvoir pré-valider une déclaration de vente"],
                ["name" => "validate-dcvt", "label" => "Pouvoir valider une déclaration de vente"],
                ["name" => "reject-dcvt", "label" => "Pouvoir rejeter une déclaration de vente"],
                ["name" => "close-dcvt", "label" => "Pouvoir clôturer une déclaration de vente"]
            ]
        ],
        [
            "name" => "Mutation",
            "code" => AvailableServiceType::MUTATION,
            "permissions" => [
                ["name" => "verify-mtn", "label" => "Pouvoir vérifier une demande de mutation"],
                ["name" => "pre-validate-mtn", "label" => "Pouvoir pré-valider une demande de mutation"],
                ["name" => "validate-mtn", "label" => "Pouvoir valider une demande de mutation"],
                ["name" => "reject-mtn", "label" => "Pouvoir rejeter une demande de mutation"],
                ["name" => "close-mtn", "label" => "Pouvoir clôturer une demande de mutation"],
                ["name" => "print-mtn-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Transformation de vehicule",
            "code" => AvailableServiceType::VEHICLE_TRANSFORMATION,
            "permissions" => [
                ["name" => "verify-vtf", "label" => "Pouvoir vérifier une demande de transformation"],
                ["name" => "pre-validate-vtf", "label" => "Pouvoir pré-valider une demande de transformation"],
                ["name" => "validate-vtf", "label" => "Pouvoir valider une demande de transformation"],
                ["name" => "reject-vtf", "label" => "Pouvoir rejeter une demande de transformation"],
                ["name" => "close-vtf", "label" => "Pouvoir clôturer une demande de transformation"],
                ["name" => "print-vtf-gray-card", "label" => "Pouvoir imprimer une carte grise"],
            ]
        ],
        [
            "name" => "Autorisation de vitre teinté",
            "code" => AvailableServiceType::TINTED_WINDOW_AUTHORIZATION,
            "permissions" => [
                ["name" => "verify-atvt", "label" => "Pouvoir vérifier une demande d'autorisation de vitre teintée"],
                ["name" => "pre-validate-atvt", "label" => "Pouvoir pré-valider une demande d'autorisation de vitre teintée"],
                ["name" => "validate-atvt", "label" => "Pouvoir valider une demande d'autorisation de vitre teintée"],
                ["name" => "reject-atvt", "label" => "Pouvoir rejeter une demande d'autorisation de vitre teintée"],
                ["name" => "close-atvt", "label" => "Pouvoir clôturer une demande d'autorisation de vitre teintée"],
                ["name" => "print-atvt-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Gravage des vitres",
            "code" => AvailableServiceType::GLASS_ENGRAVING,
            "permissions" => [
                ["name" => "verify-gdv", "label" => "Pouvoir vérifier une demande de gravage des vitres"],
                ["name" => "pre-validate-gdv", "label" => "Pouvoir pré-valider une demande de gravage des vitres"],
                ["name" => "validate-gdv", "label" => "Pouvoir valider une demande de gravage des vitres"],
                ["name" => "reject-gdv", "label" => "Pouvoir rejeter une demande de gravage des vitres"],
                ["name" => "close-gdv", "label" => "Pouvoir clôturer une demande de gravage des vitres"],
                ["name" => "print-gdv-gray-card", "label" => "Pouvoir imprimer une carte grise"]
            ]
        ],
        [
            "name" => "Dépôt de titre",
            "code" => AvailableServiceType::TITLE_DEPOSIT,
            "permissions" => [
                ["name" => "verify-dptt", "label" => "Pouvoir vérifier un dépôt de titre"],
                ["name" => "pre-validate-dptt", "label" => "Pouvoir pré-valider un dépôt de titre"],
                ["name" => "validate-dptt", "label" => "Pouvoir valider un dépôt de titre"],
                ["name" => "reject-dptt", "label" => "Pouvoir rejeter un dépôt de titre"],
                ["name" => "close-dptt", "label" => "Pouvoir clôturer un dépôt de titre"]
            ]
        ],
        [
            "name" => "Reprise de titre",
            "code" => AvailableServiceType::TITLE_RECOVERY,
            "permissions" => [
                ["name" => "verify-rptt", "label" => "Pouvoir vérifier une reprise de titre"],
                ["name" => "pre-validate-rptt", "label" => "Pouvoir pré-valider une reprise de titre"],
                ["name" => "validate-rptt", "label" => "Pouvoir valider une reprise de titre"],
                ["name" => "reject-rptt", "label" => "Pouvoir rejeter une reprise de titre"],
                ["name" => "close-rptt", "label" => "Pouvoir clôturer une reprise de titre"]
            ]
        ],
        [
            "name" => "Transformation de plaque de véhicule",
            "code" => AvailableServiceType::PLATE_TRANSFORMATION,
            "permissions" => [
                ["name" => "verify-tfmp", "label" => "Pouvoir vérifier une demande de transformation de plaque"],
                ["name" => "pre-validate-tfmp", "label" => "Pouvoir pré-valider une demande de transformation de plaque"],
                ["name" => "validate-tfmp", "label" => "Pouvoir valider une demande de transformation de plaque"],
                ["name" => "reject-tfmp", "label" => "Pouvoir rejeter une demande de transformation de plaque"],
                ["name" => "close-tfmp", "label" => "Pouvoir clôturer une demande de transformation de plaque"],
                ["name" => "print-tfmp-plate", "label" => "Pouvoir imprimer une plaque"]
            ]
        ],
        [
            "name" => "Duplicata de plaque",
            "code" => AvailableServiceType::PLATE_DUPLICATE,
            "permissions" => [
                ["name" => "verify-plate-duplicate", "label" => "Pouvoir vérifier"],
                ["name" => "pre-validate-plate-duplicate", "label" => "Pouvoir pré-valider"],
                ["name" => "validate-plate-duplicate", "label" => "Pouvoir valider"],
                ["name" => "reject-plate-duplicate", "label" => "Pouvoir rejeter"],
                ["name" => "close-plate-duplicate", "label" => "Pouvoir clôturer"]
            ]
        ],
        [
            "name" => "Duplicata de carte grise",
            "code" => AvailableServiceType::GRAY_CARD_DUPLICATE,
            "permissions" => [
                ["name" => "verify-card-duplicate", "label" => "Pouvoir vérifier"],
                ["name" => "pre-validate-card-duplicate", "label" => "Pouvoir pré-valider"],
                ["name" => "validate-card-duplicate", "label" => "Pouvoir valider"],
                ["name" => "reject-card-duplicate", "label" => "Pouvoir rejeter"],
                ["name" => "close-card-duplicate", "label" => "Pouvoir clôturer"]
            ]
        ]
    ];
}
