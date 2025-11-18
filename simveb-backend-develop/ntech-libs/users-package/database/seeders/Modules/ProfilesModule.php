<?php

namespace Ntech\UserPackage\Database\Seeders\Modules;

use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;

class ProfilesModule
{

    const PERMISSIONS = [
        [
            "name" => "Véhicule de l'état",
            "permissions" => [
                ["name" => "browse-gov-vehicle", "label" => "Consulter les véhicules de l'état"],
                ["name" => "store-gov-vehicle", "label" => "Créer un véhicule de l'état"],
                ["name" => "import-gov-vehicle", "label" => "Importer des véhicules de l'état"],
                ["name" => "show-gov-vehicle", "label" => "Consulter un véhicule de l'état"],
                ["name" => "update-gov-vehicle", "label" => "Mettre à jour un véhicule de l'état"],
                ["name" => "delete-gov-vehicle", "label" => "Supprimer un véhicule de l'état"],
            ]
        ],
        [
            "name" => "Véhicules du garage matériel des affaires intérieurs",
            "permissions" => [
                ["name" => "browse-gma-vehicle", "label" => "Consulter les véhicules du garage matériel des affaires intérieurs"],
                ["name" => "store-gma-vehicle", "label" => "Créer un véhicule du garage matériel des affaires intérieurs"],
                ["name" => "import-gma-vehicle", "label" => "Importer les véhicules du garage matériel des affaires intérieurs"],
                ["name" => "show-gma-vehicle", "label" => "Consulter un véhicule du garage matériel des affaires intérieurs"],
                ["name" => "update-gma-vehicle", "label" => "Mettre à jour un véhicule du garage matériel des affaires intérieurs"],
                ["name" => "delete-gma-vehicle", "label" => "Supprimer un véhicule du garage matériel des affaires intérieurs"],
                ["name" => "validate-gma-vehicle", "label" => "Valider un véhicule du garage matériel des affaires intérieurs"],
                ["name" => "reject-gma-vehicle", "label" => "Rejeter un véhicule du garage matériel des affaires intérieurs"],
                ["name" => "view-stats-gma-vehicle", "label" => "Consulter les statistiques des véhicules du garage matériel des affaires intérieurs"],
            ]
        ],
        [
            "name" => "Véhicules du garage matériel de la diplomatie",
            "permissions" => [
                ["name" => "browse-gmd-vehicle", "label" => "Consulter la liste des véhicules de la diplomatie"],
                ["name" => "store-gmd-vehicle", "label" => "Créer un véhicule dans le garage matériel de la diplomatie"],
                ["name" => "show-gmd-vehicle", "label" => "Consulter un véhicule du garage matériel de la diplomatie"],
                ["name" => "update-gmd-vehicle", "label" => "Mettre à jour un véhicule du garage matériel de la diplomatie"],
                ["name" => "delete-gmd-vehicle", "label" => "Supprimer un véhicule du garage matériel de la diplomatie"],
                ["name" => "validate-gmd-vehicle", "label" => "Valider un véhicule du garage matériel de la diplomatie"],
                ["name" => "reject-gmd-vehicle", "label" => "Rejeter un véhicule du garage matériel de la diplomatie"],
                ["name" => "import-gmd-vehicle", "label" => "Importer des véhicules dans le garage matériel de la diplomatie"],
            ]
        ],
        [
            "name" => "Gestion des membre de l'espace",
            "permissions" => [
                ["name" => "browse-space-staff", "label" => "Consulter la liste des membres"],
                ["name" => "store-space-staff", "label" => "Créer un membre"],
                ["name" => "show-space-staff", "label" => "Consulter les détails d'un membre"],
                ["name" => "update-space-staff", "label" => "Mettre à jour un membre"],
                ["name" => "delete-space-staff", "label" => "Supprimer un membre"],
                ["name" => "deactivate-space-staff", "label" => "Désactiver l'accès à un membre"],
                ["name" => "invite-space-staff", "label" => "Inviter un membre"],
            ]
        ],
        [
            "name" => "Gestion des véhicules deux et trois roues",
            "permissions" => [
                ["name" => "browse-motorcycle", "label" => "Consulter la liste des véhicules"],
                ["name" => "store-motorcycle", "label" => "Créer un véhicules"],
                ["name" => "show-motorcycle", "label" => "Consulter les détails d'un véhicules"],
                ["name" => "update-motorcycle", "label" => "Mettre à jour un véhicules"],
                ["name" => "delete-motorcycle", "label" => "Supprimer un véhicules"],
            ]
        ],
    ];

    const ROLES = [
        ProfileTypesEnum::central_garage->name => [
            Roles::CG_ADMIN
        ],
        ProfileTypesEnum::anatt->name => [
            Roles::ADMIN
        ],
        ProfileTypesEnum::affiliate->name => [
            Roles::AFFILIATE_ADMIN,
            Roles::AFFILIATE_HEADER,
            Roles::AFFILIATE_MEMBER,
            Roles::SERVICE_STAFF
        ],
        ProfileTypesEnum::auctioneer->name => [
            Roles::AUCTIONEER
        ],
        ProfileTypesEnum::police->name => [
            Roles::POLICE_ADMIN,

        ],
        ProfileTypesEnum::gma->name => [
            Roles::GMA_ADMIN
        ],
        ProfileTypesEnum::interpol->name => [
            Roles::INTERPOL
        ],
        ProfileTypesEnum::gmd->name => [
            Roles::GMD_ADMIN
        ],
        ProfileTypesEnum::bank->name => [
            Roles::BANK
        ],
        ProfileTypesEnum::court->name => [
            Roles::CLERK,
            Roles::INVESTIGATING_JUDGE
        ],
        ProfileTypesEnum::approved->name => [
            Roles::APPROVED_ADMIN
        ],
        ProfileTypesEnum::distributor->name => [
            Roles::DISTRIBUTOR
        ],
        ProfileTypesEnum::company->name => [
            Roles::COMPANY_ADMIN,
            Roles::COMPANY_MEMBER
        ],
    ];
}
