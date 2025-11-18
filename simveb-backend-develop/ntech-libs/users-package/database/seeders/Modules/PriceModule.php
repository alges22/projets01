<?php

namespace Ntech\UserPackage\Database\Seeders\Modules;

class PriceModule
{

    const PERMISSIONS = [
        [
            "name" => "Prix des services",
            "permissions" => [
                ["name" => "browse-price", "label" => "Consulter les prix des services"],
                ["name" => "store-price", "label" => "Créer un prix de service"],
                ["name" => "show-price", "label" => "Consulter un prix de service"],
                ["name" => "update-price", "label" => "Mettre à jour un prix de service"],
                ["name" => "delete-price", "label" => "Supprimer un prix de service"],
        ]
    ]
    ];
}
