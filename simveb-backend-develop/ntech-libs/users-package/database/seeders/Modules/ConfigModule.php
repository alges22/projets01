<?php

namespace Ntech\UserPackage\Database\Seeders\Modules;

class ConfigModule
{

    const PERMISSIONS = [
        [
            "name" => "Type d'alertes",
            "permissions" => [
                ["name" => "browse-alert-type", "label" => "Consulter les types d'alertes"],
                ["name" => "store-alert-type", "label" => "Créer un type d'alerte"],
                ["name" => "show-alert-type", "label" => "Consulter un type d'alerte"],
                ["name" => "update-alert-type", "label" => "Mettre à jour un type d'alerte"],
                ["name" => "delete-alert-type", "label" => "Supprimer un type d'alerte"],
            ]
        ],
        [
            "name" => "Modèle de numéro",
            "permissions" => [
                ["name" => "browse-number-template", "label" => "Consulter les modèles de numéro"],
                ["name" => "store-number-template", "label" => "Créer un modèle de numéro"],
                ["name" => "show-number-template", "label" => "Consulter un modèle de numéro"],
                ["name" => "update-number-template", "label" => "Mettre à jour un modèle de numéro"],
                ["name" => "delete-number-template", "label" => "Supprimer un modèle de numéro"],
            ]
        ],
        [
            "name" => "Motif de titre",
            "permissions" => [
                ["name" => "browse-title-reason", "label" => "Consulter les motifs de titre"],
                ["name" => "create-title-reason", "label" => "Consulter les types de motif de titre"],
                ["name" => "store-title-reason", "label" => "Créer un motif de titre"],
                ["name" => "show-title-reason", "label" => "Consulter un motif de titre"],
                ["name" => "update-title-reason", "label" => "Mettre à jour un motif de titre"],
                ["name" => "delete-title-reason", "label" => "Supprimer un motif de titre"],
            ]
        ],
        [
            "name" => "Type motif de titre",
            "permissions" => [
                ["name" => "browse-title-reason-type", "label" => "Consulter les types de motif de titre"],
                ["name" => "store-title-reason-type", "label" => "Créer un type de motif de titre"],
                ["name" => "show-title-reason-type", "label" => "Consulter un type de motif de titre"],
                ["name" => "update-title-reason-type", "label" => "Mettre à jour un type de motif de titre"],
                ["name" => "delete-title-reason-type", "label" => "Supprimer un type de motif de titre"],
            ]
        ],
        [
            'name' => 'Motif de ré-immatriculation',
            'permissions' => [
                ['name' => 'browse-reimmatriculation-reason', 'label' => "Consulter les motifs de ré-immatriculation"],
                ['name' => 'show-reimmatriculation-reason', 'label' => "Consulter un motif de ré-immatriculation"],
                ['name' => 'update-reimmatriculation-reason', 'label' => "Modifier un motif de ré-immatriculation"],
            ]
        ],
        [
            'name' => "Type d'immatriculation",
            'permissions' => [
                ["name" => "browse-immatriculation-type", "label" => "Consulter les types d'immatriculation"],
                ["name" => "store-immatriculation-type", "label" => "Créer un type d'immatriculation"],
                ["name" => "show-immatriculation-type", "label" => "Consulter un type d'immatriculation"],
                ["name" => "update-immatriculation-type", "label" => "Mettre à jour un type d'immatriculation"],
                ["name" => "delete-immatriculation-type", "label" => "Supprimer un type d'immatriculation"],
            ]
        ],
        [
            "name" => "Type de transformation de véhicule",
            "permissions" => [
                ['name' => 'browse-transformation-type', 'label' => "Consulter la liste des types de transformation"],
                ['name' => 'store-transformation-type', 'label' => "Créer un type de transformation"],
                ['name' => 'show-transformation-type', 'label' => "Consulter un type de  transformation"],
                ['name' => 'update-transformation-type', 'label' => "Mettre à jour un type de transformation"],
                ['name' => 'delete-transformation-type', 'label' => "Supprimer un type de transformation"],
            ]
        ],
    ];
}
