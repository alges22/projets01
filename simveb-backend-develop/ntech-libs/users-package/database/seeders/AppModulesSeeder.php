<?php

namespace Ntech\UserPackage\Database\Seeders;

use Illuminate\Database\Seeder;
use Ntech\UserPackage\Database\Seeders\Modules\ConfigModule;
use Ntech\UserPackage\Database\Seeders\Modules\PriceModule;
use Ntech\UserPackage\Database\Seeders\Modules\ProfilesModule;
use Ntech\UserPackage\Database\Seeders\Modules\ServicesModule;
use Ntech\UserPackage\Models\Module;
use Spatie\Permission\Models\Permission;

class AppModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = [
            ...PriceModule::PERMISSIONS,
            ...ConfigModule::PERMISSIONS,
            ...ProfilesModule::PERMISSIONS,
            ...ServicesModule::PERMISSIONS,
            [
                "name" => "Tableau de bord",
                "permissions" => [
                    ["name" => "dashboard", "label" => "Accéder au tableau de bord"],
                ]
            ],
            [
                "name" => "Personnels",
                "permissions" => [
                    ["name" => "browse-staff", "label" => "Parcourir le personnel"],
                    ["name" => "show-staff", "label" => "Consultation d'un membre du personnel"],
                    ["name" => "store-staff", "label" => "Création d'un membre du personnel"],
                    ["name" => "update-staff", "label" => "Mise à jour d'un membre du personnel"],
                    ["name" => "delete-staff", "label" => "Suppression d'un membre du personnel"],
                    ["name" => "search-staff", "label" => "Rechercher un membre du personnel"],
                ]
            ],
            [
                "name" => "Véhicules",
                "permissions" => [
                    ["name" => "show-vehicle", "label" => "Consulter un véhicule"],
                ]
            ],
            [
                "name" => "Services",
                "permissions" => [
                    ["name" => "browse-service", "label" => "Consulter les services"],
                    ["name" => "show-service", "label" => "Consulter un service"],
                    ["name" => "store-service", "label" => "Créer un service"],
                    ["name" => "update-service", "label" => "Mettre à jour un service"],
                    ["name" => "delete-service", "label" => "Supprimer un service"],
                ]
            ],
            [
                "name" => "Journal",
                "permissions" => [
                    ["name" => "browse-activity-log", "label" => "Parcourir le journal d'activité"],
                ]
            ],
            [
                "name" => "Type de document",
                "permissions" => [
                    ["name" => "browse-document-type", "label" => "Consulter les types de documents"],
                    ["name" => "store-document-type", "label" => "Créer un type de document"],
                    ["name" => "show-document-type", "label" => "Consulter un type de document"],
                    ["name" => "update-document-type", "label" => "Mettre à jour un type de document"],
                    ["name" => "delete-document-type", "label" => "Supprimer un type de document"],
                ]
            ],
            [
                "name" => "Type de document requis",
                "permissions" => [
                    ["name" => "browse-required-document-type", "label" => "Consulter les types de documents requis"],
                    ["name" => "store-required-document-type", "label" => "Créer un type de document requis"],
                    ["name" => "show-required-document-type", "label" => "Consulter un type de document requis"],
                    ["name" => "update-required-document-type", "label" => "Mettre à jour un type de document requis"],
                    ["name" => "delete-required-document-type", "label" => "Supprimer un type de document requis"],
                ]
            ],
            [
                "name" => "Couleurs de plaque",
                "permissions" => [
                    ["name" => "browse-plate-color", "label" => "Consulter les couleurs de plaque"],
                    ["name" => "store-plate-color", "label" => "Créer une couleur de plaque"],
                    ["name" => "show-plate-color", "label" => "Consulter une couleur de plaque"],
                    ["name" => "update-plate-color", "label" => "Mettre à jour une couleur de plaque"],
                    ["name" => "delete-plate-color", "label" => "Supprimer une couleur de plaque"],
                ]
            ],
            [
                "name" => "Catégories de caractéristique de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-characteristic-category", "label" => "Consulter les catégories de caractéristiques de véhicule"],
                    ["name" => "store-vehicle-characteristic-category", "label" => "Créer une catégorie de caractéristique de véhicule"],
                    ["name" => "show-vehicle-characteristic-category", "label" => "Consulter une catégorie de caractéristique de véhicule"],
                    ["name" => "update-vehicle-characteristic-category", "label" => "Mettre à jour une catégorie de caractéristique de véhicule"],
                    ["name" => "delete-vehicle-characteristic-category", "label" => "Supprimer une catégorie de caractéristique de véhicule"],
                    ["name" => "update-vehicle-characteristic-category-field-name", "label" => "Mettre à jour le code de la catégorie de charactéristique"],
                ]
            ],
            [
                "name" => "Caractéristiques de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-characteristic", "label" => "Consulter les caractéristiques de véhicule"],
                    ["name" => "store-vehicle-characteristic", "label" => "Créer une caractéristique de véhicule"],
                    ["name" => "show-vehicle-characteristic", "label" => "Consulter une caractéristique de véhicule"],
                    ["name" => "update-vehicle-characteristic", "label" => "Mettre à jour une caractéristique de véhicule"],
                    ["name" => "delete-vehicle-characteristic", "label" => "Supprimer une caractéristique de véhicule"],
                ]
            ],
            [
                "name" => "Forme de plaque",
                "permissions" => [
                    ["name" => "browse-plate-shape", "label" => "Consulter les formes de plaque"],
                    ["name" => "store-plate-shape", "label" => "Créer une forme de plaque"],
                    ["name" => "show-plate-shape", "label" => "Consulter une forme de plaque"],
                    ["name" => "update-plate-shape", "label" => "Mettre à jour une forme de plaque"],
                    ["name" => "delete-plate-shape", "label" => "Supprimer une forme de plaque"],
                ]
            ],
            [
                "name" => "Demande d'enregistrement d'affilié",
                "permissions" => [
                    ["name" => "browse-space-registration-request", "label" => "Consulter les demande d'enregistrement d'affiliés"],
                    ["name" => "store-space-registration-request", "label" => "Créer une demande d'enregistrement d'affilié"],
                    ["name" => "show-space-registration-request", "label" => "Consulter une demande d'enregistrement d'affilié"],
                    ["name" => "validate-space-registration-request", "label" => "Valider une demande d'enregistrement d'affilié"],
                    ["name" => "reject-space-registration-request", "label" => "Rejeter une demande d'enregistrement d'affilié"],
                    ["name" => "delete-space-registration-request", "label" => "Supprimer une demande d'enregistrement d'affilié"],
                ]
            ],
            [
                "name" => "Affilié",
                "permissions" => [
                    ["name" => "browse-space", "label" => "Consulter les affiliés"],
                    ["name" => "show-space", "label" => "Consulter un affilié"],
                    ["name" => "update-space", "label" => "Mettre à jour un affilié"],
                    ["name" => "delete-space", "label" => "Supprimer un affilié"],
                ]
            ],
            [
                'name' => "Agent affilié",
                'permissions' => [
                    ['name' => 'browse-space-staff', 'label' => 'Consulter les membres du personnel affilié'],
                ]
            ],
            [
                "name" => "Type de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-type", "label" => "Consulter les types de véhicule"],
                    ["name" => "store-vehicle-type", "label" => "Créer un type de véhicule"],
                    ["name" => "show-vehicle-type", "label" => "Consulter un type de véhicule"],
                    ["name" => "update-vehicle-type", "label" => "Mettre à jour un type de véhicule"],
                    ["name" => "delete-vehicle-type", "label" => "Supprimer un type de véhicule"],
                ]
            ],
            [
                "name" => "Gestion des parcs",
                "permissions" => [
                    ["name" => "browse-park", "label" => "Consulter la liste des  parcs"],
                    ["name" => "store-park", "label" => "Créer un parc"],
                    ["name" => "show-park", "label" => "Consulter les données d'un parc"],
                    ["name" => "update-park", "label" => "Mettre à jour les données d'un parc"],
                    ["name" => "delete-park", "label" => "Supprimer les données d'un parc"],
                ]
            ],
            [
                "name" => "Gestion des frontières",
                "permissions" => [
                    ["name" => "browse-border", "label" => "Consulter la liste des frontières"],
                    ["name" => "store-border", "label" => "Ajouter une frontière"],
                    ["name" => "show-border", "label" => "Consulter une frontières"],
                    ["name" => "update-border", "label" => "Mettre à jour les données d'une frontière"],
                    ["name" => "delete-border", "label" => "Supprimer une frontière"],
                ]
            ],
            [
                "name" => "Type de centre de gestion",
                "permissions" => [
                    ["name" => "browse-management-center-type", "label" => "Consulter les types de centre de gestion"],
                    ["name" => "store-management-center-type", "label" => "Créer un type de centre de gestion"],
                    ["name" => "show-management-center-type", "label" => "Consulter un type de centre de gestion"],
                    ["name" => "update-management-center-type", "label" => "Mettre à jour un type de centre de gestion"],
                    ["name" => "delete-management-center-type", "label" => "Supprimer un type de centre de gestion"],
                ]
            ],
            [
                "name" => "Centre de gestion",
                "permissions" => [
                    ["name" => "browse-management-center", "label" => "Consulter les centres de gestion"],
                    ["name" => "store-management-center", "label" => "Créer un centre de gestion"],
                    ["name" => "show-management-center", "label" => "Consulter un centre de gestion"],
                    ["name" => "update-management-center", "label" => "Mettre à jour un centre de gestion"],
                    ["name" => "delete-management-center", "label" => "Supprimer un centre de gestion"],
                ]
            ],
            [
                "name" => "Déclarant",
                "permissions" => [
                    ["name" => "browse-declarant", "label" => "Consulter la liste des déclarants"],
                    ["name" => "store-declarant", "label" => "Ajouter un déclarant"],
                    ["name" => "show-declarant", "label" => "Consulter les données d'un déclarant"],
                    ["name" => "update-declarant", "label" => "Mettre à jour les données d'un déclarant"],
                    ["name" => "delete-declarant", "label" => "Supprimer les données d'un déclarant"],
                ]
            ],
            [
                "name" => "Situation administrative d'un véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-administrative-status", "label" => "Consulter la liste de la situation administrative des véhicules"],
                    ["name" => "show-vehicle-administrative-status", "label" => "Consulter la situation administrative d'un véhicule"],
                ]
            ],
            [
                "name" => "Type de client",
                "permissions" => [
                    ["name" => "browse-client-type", "label" => "Consulter les types de client"],
                    ["name" => "store-client-type", "label" => "Créer un type de client"],
                    ["name" => "show-client-type", "label" => "Consulter un type de client"],
                    ["name" => "update-client-type", "label" => "Mettre à jour un type de client"],
                    ["name" => "delete-client-type", "label" => "Supprimer un type de client"],
                ]
            ],
            [
                "name" => "Catégorie de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-category", "label" => "Consulter les catégories de véhicule"],
                    ["name" => "store-vehicle-category", "label" => "Créer une catégorie de véhicule"],
                    ["name" => "show-vehicle-category", "label" => "Consulter une catégorie de véhicule"],
                    ["name" => "update-vehicle-category", "label" => "Mettre à jour une catégorie de véhicule"],
                    ["name" => "delete-vehicle-category", "label" => "Supprimer une catégorie de véhicule"],
                ]
            ],
            [
                "name" => "Numéro de plaque réservé",
                "permissions" => [
                    ["name" => "browse-reserved-plate-number", "label" => "Consulter les numéros de plaque réservé"],
                    ["name" => "store-reserved-plate-number", "label" => "Créer un numéro de plaque réservé"],
                    ["name" => "show-reserved-plate-number", "label" => "Consulter un numéro de plaque réservé"],
                    ["name" => "update-reserved-plate-number", "label" => "Mettre à jour un numéro de plaque réservé"],
                    ["name" => "delete-reserved-plate-number", "label" => "Supprimer un numéro de plaque réservé"],
                    ["name" => "validate-reserved-plate-number", "label" => "Valider un numéro de plaque réservé"],
                ]
            ],
            [
                "name" => "Commission",
                "permissions" => [
                    ["name" => "browse-commission", "label" => "Consulter les commissions"],
                    ["name" => "store-commission", "label" => "Créer une commission"],
                    ["name" => "show-commission", "label" => "Consulter une commission"],
                    ["name" => "update-commission", "label" => "Mettre à jour une commission"],
                    ["name" => "delete-commission", "label" => "Supprimer une commission"],
                ]
            ],
            [
                "name" => "Source d'énergie de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-energy-source", "label" => "Consulter les sources d'énergie de véhicule"],
                    ["name" => "store-vehicle-energy-source", "label" => "Créer une source d'énergie de véhicule"],
                    ["name" => "show-vehicle-energy-source", "label" => "Consulter une source d'énergie de véhicule"],
                    ["name" => "update-vehicle-energy-source", "label" => "Mettre à jour une source d'énergie de véhicule"],
                    ["name" => "delete-vehicle-energy-source", "label" => "Supprimer une source d'énergie de véhicule"],
                ]
            ],
            [
                "name" => "Notification",
                "permissions" => [
                    ["name" => "browse-notification-config", "label" => "Consulter les configurations de notifications"],
                    ["name" => "show-notification-config", "label" => "Consulter une configuration de notification"],
                    ["name" => "update-notification-config", "label" => "Mettre à jour une configuration de notification"],
                ]
            ],
            [
                "name" => "Services offerts par l'ANATT",
                "permissions" => [
                    ["name" => "browse-service", "label" => "Consulter les services offerts"],
                    ["name" => "store-service", "label" => "Créer un service"],
                    ["name" => "show-service", "label" => "Consulter un service"],
                    ["name" => "update-service", "label" => "Mettre à jour un service"],
                    ["name" => "delete-service", "label" => "Supprimer un service"],
                ]
            ],
            [
                "name" => "Type de services offerts par l'ANATT",
                "permissions" => [
                    ["name" => "browse-service-type", "label" => "Consulter les type de services offerts"],
                    ["name" => "store-service-type", "label" => "Créer un type de service"],
                    ["name" => "show-service-type", "label" => "Consulter un type de service"],
                    ["name" => "update-service-type", "label" => "Mettre à jour un type de service"],
                    ["name" => "delete-service-type", "label" => "Supprimer un type de service"],
                ]
            ],
            [
                "name" => "Puissance de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-power", "label" => "Consulter les puissances de véhicule"],
                    ["name" => "store-vehicle-power", "label" => "Créer une puissance de véhicule"],
                    ["name" => "show-vehicle-power", "label" => "Consulter une puissance de véhicule"],
                    ["name" => "update-vehicle-power", "label" => "Mettre à jour une puissance de véhicule"],
                    ["name" => "delete-vehicle-power", "label" => "Supprimer une puissance de véhicule"],
                ]
            ],
            [
                "name" => "Marques de véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-brand", "label" => "Consulter les marques de véhicule"],
                    ["name" => "store-vehicle-brand", "label" => "Créer une marque de véhicule"],
                    ["name" => "show-vehicle-brand", "label" => "Consulter une marque de véhicule"],
                    ["name" => "update-vehicle-brand", "label" => "Mettre à jour une marque de véhicule"],
                    ["name" => "delete-vehicle-brand", "label" => "Supprimer une marque de véhicule"],
                ]
            ],
            [
                "name" => "Personnes sur la liste noire",
                "permissions" => [
                    ["name" => "browse-blacklist-person", "label" => "Consulter les personnes sur la liste noire"],
                    ["name" => "store-blacklist-person", "label" => "Ajouter une personne sur la liste noire"],
                    ["name" => "show-blacklist-person", "label" => "Consulter une personne sur la liste noire"],
                    ["name" => "update-blacklist-person", "label" => "Mettre à jour une personne de la liste noire"],
                    ["name" => "delete-blacklist-person", "label" => "Supprimer une personne de la liste noire"],
                ]
            ],
            [
                "name" => "Type de propriétaire",
                "permissions" => [
                    ["name" => "browse-owner-type", "label" => "Consulter les types de propriétaire"],
                    ["name" => "store-owner-type", "label" => "Créer un type de propriétaire"],
                    ["name" => "show-owner-type", "label" => "Consulter un type de propriétaire"],
                    ["name" => "update-owner-type", "label" => "Mettre à jour un type de propriétaire"],
                    ["name" => "delete-owner-type", "label" => "Supprimer un type de propriétaire"],
                ]
            ],
            [
                "name" => "Zones géographiques",
                "permissions" => [
                    ["name" => "browse-geographical-area", "label" => "Consulter les zones géographiques"],
                    ["name" => "store-geographical-area", "label" => "Créer une zone géographique"],
                    ["name" => "show-geographical-area", "label" => "Consulter une zone géographique"],
                    ["name" => "update-geographical-area", "label" => "Mettre à jour une zone géographique"],
                    ["name" => "delete-geographical-area", "label" => "Supprimer une zone géographique"],
                ]
            ],
            [
                "name" => "Autorisation de vitres teintées",
                "permissions" => [
                    ["name" => "browse-tinted-windows-authorization", "label" => "Consulter les demandes d'autorisations de vitres teintées"],
                    ["name" => "store-tinted-windows-authorization", "label" => "Créer une demande d'autorisation de vitres teintées"],
                    ["name" => "show-tinted-windows-authorization", "label" => "Consulter une demande d'autorisation de vitres teintées"],
                    ["name" => "update-tinted-windows-authorization", "label" => "Mettre à jour une demande d'autorisation de vitres teintées"],
                    ["name" => "delete-tinted-windows-authorization", "label" => "Supprimer une demande d'autorisation de vitres teintées"],
                ]
            ],
            [
                "name" => "Cartes grises internationales",
                "permissions" => [
                    ["name" => "browse-international-vehicle-registration-document", "label" => "Consulter les demandes de carte grise internationale"],
                    ["name" => "store-international-vehicle-registration-document", "label" => "Créer une demande de carte grise internationale"],
                    ["name" => "show-international-vehicle-registration-document", "label" => "Consulter une demande de carte grise internationale"],
                    ["name" => "update-international-vehicle-registration-document", "label" => "Mettre à jour une demande de carte grise internationale"],
                    ["name" => "delete-international-vehicle-registration-document", "label" => "Supprimer une demande de carte grise internationale"],
                ]
            ],
            [
                "name" => "Format d'immatriculation",
                "permissions" => [
                    ["name" => "browse-immatriculation-format", "label" => "Consulter les demandes de carte grise internationale"],
                    ["name" => "store-immatriculation-format", "label" => "Créer une demande de carte grise internationale"],
                    ["name" => "show-immatriculation-format", "label" => "Consulter une demande de carte grise internationale"],
                    ["name" => "update-immatriculation-format", "label" => "Mettre à jour une demande de carte grise internationale"],
                    ["name" => "delete-immatriculation-format", "label" => "Supprimer une demande de carte grise internationale"],
                ]
            ],
            [
                "name" => "Format d'immatriculation",
                "permissions" => [
                    ["name" => "browse-im-format", "label" => "Consulter les formats d'immatriculation"],
                    ["name" => "store-im-format", "label" => "Créer un format d'immatriculation"],
                    ["name" => "show-im-format", "label" => "Consulter un format d'immatriculation"],
                    ["name" => "update-im-format", "label" => "Mettre à jour un format d'immatriculation"],
                    ["name" => "delete-im-format", "label" => "Supprimer un format d'immatriculation"],
                ]
            ],
            [
                "name" => "Roles",
                "permissions" => [
                    ["name" => "browse-role", "label" => "Consulter les roles"],
                    ["name" => "store-role", "label" => "Créer un role"],
                    ["name" => "show-role", "label" => "Consulter un role"],
                    ["name" => "update-role", "label" => "Mettre à jour un role"],
                    ["name" => "delete-role", "label" => "Supprimer un role"],
                ]
            ],
            [
                "name" => "Permissions",
                "permissions" => [
                    ["name" => "browse-permission", "label" => "Consulter les permissions"],
                    ["name" => "store-permission", "label" => "Créer une permission"],
                    ["name" => "show-permission", "label" => "Consulter une permission"],
                    ["name" => "update-permission", "label" => "Mettre à jour une permission"],
                    ["name" => "delete-permission", "label" => "Supprimer une permission"],
                ]
            ],
            [
                "name" => "Demandes d'immatriculation",
                "permissions" => [
                    ["name" => "browse-im-demand", "label" => "Consulter les demandes d'immatriculation"],
                    ["name" => "store-im-demand", "label" => "Créer une demande d'immatriculation"],
                    ["name" => "show-im-demand", "label" => "Consulter une demande d'immatriculation"],
                    ["name" => "update-im-demand", "label" => "Mettre à jour une demande d'immatriculation"],
                    ["name" => "delete-im-demand", "label" => "Supprimer une demande d'immatriculation"],
                    ["name" => "verify-im-demand", "label" => "Pouvoir vérifier une demande"],
                    ["name" => "pre-validate-im-demand", "label" => "Pré-valider une demande"],
                    ["name" => "validate-im-demand", "label" => "Valider une demande "],
                    ["name" => "reject-im-demand", "label" => "Rejeter une demande "],
                    ["name" => "assign-to-staff-im-demand", "label" => "Assigner une demande à un agent"],
                    ["name" => "affect-to-service-im-demand", "label" => "Affecter une demande à un service"],
                    ["name" => "affect-to-interpol-im-demand", "label" => "Affecter une demande à interpole"],
                    ["name" => "assign-to-interpol-im-demand", "label" => "Assigner une demande à un agent d'interpole"],
                    ["name" => "emit-print-order-im-demand", "label" => "Émettre l'ordre de validation"],
                    ["name" => "print-im-demand", "label" => "Imprimer une plaque"],
                    ["name" => "interpol-validate-im-demand", "label" => "Validation interpole"],
                    ["name" => "interpol-pre-validate-im-demand", "label" => "Pré-validation interpole"],
                    ["name" => "reject-interpol-im-demand", "label" => "Rejet interpole"],
                    ["name" => "control-anatt-im-demand", "label" => "Contrôle de l'ANATT"],
                ]
            ],
            [
                "name" => "Demandes de duplicata de carte grise",
                "permissions" => [
                    ["name" => "browse-card-duplicate", "label" => "Consulter les demandes de duplicate"],
                    ["name" => "store-card-duplicate", "label" => "Créer une demande de duplicata"],
                    ["name" => "show-card-duplicate", "label" => "Consulter une demande de duplicata"],
                    ["name" => "update-card-duplicate", "label" => "Mettre à jour une demande d'immatriculation"],
                    ["name" => "delete-card-duplicate", "label" => "Supprimer une demande d'immatriculation"],
                    ["name" => "verify-card-duplicate", "label" => "Pouvoir vérifier une demande"],
                    ["name" => "pre-validate-card-duplicate", "label" => "Pré-valider une demande"],
                    ["name" => "validate-card-duplicate", "label" => "Valider une demande"],
                    ["name" => "assign-to-staff-card-duplicate", "label" => "Assigner une demande à un agent"],
                    ["name" => "affect-to-service-card-duplicate", "label" => "Affecter une demande à un service"],
                    ["name" => "emit-print-order-card-duplicate", "label" => "Émettre l'ordre de validation"],
                    ["name" => "print-card-duplicate", "label" => "Imprimer une plaque"],
                ]
            ],
            [
                "name" => "Demandes de duplicata de plaque",
                "permissions" => [
                    ["name" => "browse-plate-duplicate", "label" => "Consulter les demandes de duplicate"],
                    ["name" => "store-plate-duplicate", "label" => "Créer une demande de duplicata"],
                    ["name" => "show-plate-duplicate", "label" => "Consulter une demande de duplicata"],
                    ["name" => "update-plate-duplicate", "label" => "Mettre à jour une demande d'immatriculation"],
                    ["name" => "delete-plate-duplicate", "label" => "Supprimer une demande d'immatriculation"],
                    ["name" => "verify-plate-duplicate", "label" => "Pouvoir vérifier une demande"],
                    ["name" => "pre-validate-plate-duplicate", "label" => "Pré-valider une demande"],
                    ["name" => "validate-plate-duplicate", "label" => "Valider une demande "],
                    ["name" => "assign-to-staff-plate-duplicate", "label" => "Assigner une demande à un agent"],
                    ["name" => "affect-to-service-plate-duplicate", "label" => "Affecter une demande à un service"],
                    ["name" => "emit-print-order-plate-duplicate", "label" => "Émettre l'ordre de validation"],
                    ["name" => "print-plate-duplicate", "label" => "Imprimer une plaque"],
                ]
            ],
            [
                'name' => 'Configurations',
                'permissions' => [
                    ['name' => 'access-config', 'label' => 'Accès aux configurations'],
                ]
            ],
            [
                'name' => 'Metadata',
                'permissions' => [
                    ['name' => 'browse-meta-data', 'label' => 'Consulter les meta data'],
                    ['name' => 'search-meta-data', 'label' => 'Rechercher une meta donnée'],
                    ['name' => 'update-meta-data', 'label' => 'Modifier les meta data'],
                ]
            ],
            [
                'name' => "Demande d'impression",
                'permissions' => [
                    ['name' =>  'browse-impression-demand', 'label' => "Consulter les demandes d'impression"],
                    ['name' =>  'show-impression-demand', 'label' => "Consulter une demande d'impression"],
                    ['name' =>  'store-impression-demand', 'label' => "Créer une demande d'impression"],
                    ['name' =>  'validate-impression-demand', 'label' => "Validate une demande d'impression"],
                    ['name' =>  'reject-impression-demand', 'label' => "Rejeter une demande d'impression"],
                    ['name' =>  'confirm-impression-demand', 'label' => "Confirmer une demande d'impression"],
                ]
            ],
            [
                'name' => "Commande de plaque",
                'permissions' => [
                    ['name' =>  'browse-plate-order', 'label' => "Consulter les commandes de plaque"],
                    ['name' =>  'show-plate-order', 'label' => "Consulter une commande de plaque"],
                    ['name' =>  'store-plate-order', 'label' => "Créer une commande de plaque"],
                    ['name' =>  'validate-plate-order', 'label' => "Validate une commande de plaque"],
                    ['name' =>  'reject-plate-order', 'label' => "Rejeter une commande de plaque"],
                ]
            ],
            [
                'name' => "Plaque",
                'permissions' => [
                    ['name' =>  'browse-plate', 'label' => "Consulter les plaques"],
                    ['name' =>  'show-plate', 'label' => "Consulter une plaque"],
                    ['name' =>  'store-plate', 'label' => "Enregistrer une plaque"],
                    ['name' =>  'confirm-plate-reception', 'label' => "Confirmer la reception de la plaque"],
                    ['name' =>  'stats-plate', 'label' => "Consulter les statistiques des plaques"],
                ]
            ],
            [
                'name' => 'Statistiques',
                'permissions' => [
                    ['name' => 'browse-stats', 'label' => "Consulter les statistiques"],
                    ['name' => 'browse-immatriculation-demand-stats', 'label' => "Consulter les statistiques de demande d'immatriculation"],
                    ['name' => 'browse-duplicate-demand-stats', 'label' => "Consulter les statistiques de demande de duplicata"],
                ]
            ],
            [
                "name" => "Zones",
                "permissions" => [
                    ["name" => "browse-zone", "label" => "Consulter la liste des zones"],
                    ["name" => "store-zone", "label" => "Créer une zone"],
                    ["name" => "show-zone", "label" => "Consulter une zone"],
                    ["name" => "update-zone", "label" => "Mettre à jour une zone"],
                    ["name" => "delete-zone", "label" => "Supprimer une zone"],
                ]
            ],
            [
                "name" => "Commune",
                "permissions" => [
                    ["name" => "browse-town", "label" => "Consulter la liste des Commune"],
                    ["name" => "store-town", "label" => "Créer une Commune"],
                    ["name" => "show-town", "label" => "Consulter une Commune"],
                    ["name" => "update-town", "label" => "Mettre à jour une Commune"],
                    ["name" => "delete-town", "label" => "Supprimer une Commune"],
                ]
            ],
            [
                "name" => "Arrondissement",
                "permissions" => [
                    ["name" => "browse-district", "label" => "Consulter la liste des Arrondissements"],
                    ["name" => "store-district", "label" => "Créer un Arrondissement"],
                    ["name" => "show-district", "label" => "Consulter un Arrondissement"],
                    ["name" => "update-district", "label" => "Mettre à jour un Arrondissement"],
                    ["name" => "delete-district", "label" => "Supprimer un Arrondissement"],
                ]
            ],
            [
                "name" => "Village",
                "permissions" => [
                    ["name" => "browse-village", "label" => "Consulter la liste des villages"],
                    ["name" => "store-village", "label" => "Créer un village"],
                    ["name" => "show-village", "label" => "Consulter un village"],
                    ["name" => "update-village", "label" => "Mettre à jour un village"],
                    ["name" => "delete-village", "label" => "Supprimer un village"],
                ]
            ],
            [
                "name" => "Type d'institution",
                "permissions" => [
                    ["name" => "browse-institution-type", "label" => "Consulter la liste des types d'institution"],
                    ["name" => "store-institution-type", "label" => "Créer un type d'institution"],
                    ["name" => "show-institution-type", "label" => "Consulter un type d'institution"],
                    ["name" => "update-institution-type", "label" => "Mettre à jour un type d'institution"],
                    ["name" => "delete-institution-type", "label" => "Supprimer un type d'institution"],
                ]
            ],
            [
                "name" => "Institution",
                "permissions" => [
                    ["name" => "browse-institution", "label" => "Consulter la liste des institutions"],
                    ["name" => "store-institution", "label" => "Créer une institution"],
                    ["name" => "show-institution", "label" => "Consulter une institution"],
                    ["name" => "update-institution", "label" => "Mettre à jour une institution"],
                    ["name" => "delete-institution", "label" => "Supprimer une institution"],
                ]
            ],
            [
                "name" => "Invitation",
                "permissions" => [
                    ["name" => "browse-invitation", "label" => "Consulter la liste des invitations"],
                    ["name" => "store-invitation", "label" => "Créer une invitation"],
                    ["name" => "show-invitation", "label" => "Consulter une invitation"],
                    ["name" => "validate-invitation", "label" => "Valider une invitation"],
                    ["name" => "deny-invitation", "label" => "Refuser une invitation"],
                ]
            ],
            [
                "name" => "Type de profile",
                "permissions" => [
                    ["name" => "browse-profile-type", "label" => "Consulter la liste des types de profile"],
                    ["name" => "store-profile-type", "label" => "Créer un type de profile"],
                    ["name" => "show-profile-type", "label" => "Consulter un type de profile"],
                    ["name" => "update-profile-type", "label" => "Mettre à jour un type de profile"],
                ]
            ],
            [
                "name" => "Organisation",
                "permissions" => [
                    ["name" => "browse-organization", "label" => "Consulter la liste des organisations"],
                    ["name" => "store-organization", "label" => "Créer une organisation"],
                    ["name" => "show-organization", "label" => "Consulter une organisation"],
                    ["name" => "update-organization", "label" => "Mettre à jour une organisation"],
                    ["name" => "delete-organization", "label" => "Supprimer une organisation"],
                ]
            ],
            [
                "name" => "Passage d'un véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-passage", "label" => "Consulter la liste des passages"],
                    ["name" => "store-vehicle-passage", "label" => "Créer un passage"],
                    ["name" => "show-vehicle-passage", "label" => "Consulter un passage"],
                    ["name" => "update-vehicle-passage", "label" => "Mettre à jour un passage"],
                    ["name" => "delete-vehicle-passage", "label" => "Supprimer un passage"],
                ]
            ],
            [
                "name" => "Alerte sur un véhicule",
                "permissions" => [
                    ["name" => "browse-vehicle-alert", "label" => "Consulter la liste des alertes"],
                    ["name" => "store-vehicle-alert", "label" => "Créer une alerte"],
                    ["name" => "show-vehicle-alert", "label" => "Consulter une alerte"],
                    ["name" => "update-vehicle-alert", "label" => "Mettre à jour une alerte"],
                    ["name" => "delete-vehicle-alert", "label" => "Supprimer une alerte"],
                ]
            ],
            [
                "name" => "Véhicule sur la liste noire",
                "permissions" => [
                    ["name" => "browse-blacklist-vehicle", "label" => "Consulter les véhicules su la liste noire"],
                    ["name" => "store-blacklist-vehicle", "label" => "Ajouter un véhicule sur la liste noire"],
                    ["name" => "show-blacklist-vehicle", "label" => "Consulter un véhicule sur la liste noire"],
                    ["name" => "validate-blacklist-vehicle", "label" => "Valider un véhicule sur la liste noire"],
                    ["name" => "reject-blacklist-vehicle", "label" => "Rejeter un véhicule de la liste noire"],
                    ["name" => "delete-blacklist-vehicle", "label" => "Supprimer un véhicule de la liste noire"],
                ]
            ],
            [
                "name" => "Commande",
                "permissions" => [
                    ["name" => "browse-order", "label" => "Consulter la liste des commandes"],
                    ["name" => "show-order", "label" => "Consulter un commande"],
                ]
            ],
            [
                'name' => 'Déclaration de vente aux enchères',
                'permissions' => [
                    ['name' => 'browse-auction-sale-declaration', 'label' => "Consulter la liste des déclarations de vente aux enchères"],
                    ['name' => 'store-auction-sale-declaration', 'label' => "Créer une déclaration de vente aux enchères"],
                    ['name' => 'show-auction-sale-declaration', 'label' => "Consulter une déclaration de vente aux enchères"],
                    ['name' => 'update-auction-sale-declaration', 'label' => "Mettre à jour une déclaration de vente aux enchères"],
                    ['name' => 'delete-auction-sale-declaration', 'label' => "Supprimer une déclaration de vente aux enchères"],
                ],
            ],
            [
                'name' => 'Véhicule vendu aux enchères',
                'permissions' => [
                    ['name' => 'browse-auction-sale-vehicle', 'label' => "Consulter la liste des véhicules vendu aux enchères"],
                    ['name' => 'store-auction-sale-vehicle', 'label' => "Créer un véhicule vendu aux enchères"],
                    ['name' => 'show-auction-sale-vehicle', 'label' => "Consulter un véhicule vendu aux enchères"],
                    ['name' => 'update-auction-sale-vehicle', 'label' => "Mettre à jour un véhicule vendu aux enchères"],
                    ['name' => 'delete-auction-sale-vehicle', 'label' => "Supprimer un véhicule vendu aux enchères"],
                ],
            ],
            [
                'name' => 'Déclaration de réforme',
                'permissions' => [
                    ['name' => 'browse-reform-declaration', 'label' => "Consulter la liste des réformes"],
                    ['name' => 'store-reform-declaration', 'label' => "Créer une déclaration de réforme"],
                    ['name' => 'show-reform-declaration', 'label' => "Consulter une déclaration de réforme"],
                    ['name' => 'update-reform-declaration', 'label' => "Mettre à jour une déclaration de réforme"],
                    ['name' => 'delete-reform-declaration', 'label' => "Supprimer une déclaration de réforme"],
                ],
            ],
            [
                "name" => "Demande d'accréditation",
                "permissions" => [
                    ['name' => 'browse-accreditation', 'label' => "Consulter la liste des demandes d'accréditation"],
                    ['name' => 'store-accreditation', 'label' => "Créer une demande d'accréditation"],
                    ['name' => 'show-accreditation', 'label' => "Consulter une demande d'accréditation"],
                    ['name' => 'update-accreditation', 'label' => "Mettre à jour une demande d'accréditation"],
                    ['name' => 'delete-accreditation', 'label' => "Supprimer une demande d'accréditation"],
                    ['name' => 'validate-accreditation', 'label' => "Valider une demande d'accréditation"],
                    ['name' => 'reject-accreditation', 'label' => "Rejeter une demande d'accréditation"],
                ],
            ],
            [
                "name" => "Demande d'affectation de policier à une frontière",
                "permissions" => [
                    ['name' => 'browse-police-officer-assignment', 'label' => "Consulter la liste des demandes d'affectation de policiers à une frontière"],
                    ['name' => 'store-police-officer-assignment', 'label' => "Créer une demande d'affectation de policier à une frontière"],
                    ['name' => 'show-police-officer-assignment', 'label' => "Consulter une demande d'affectation de policier à une frontière"],
                    ['name' => 'update-police-officer-assignment', 'label' => "Mettre à jour une demande d'affectation de policier à une frontière"],
                    ['name' => 'delete-police-officer-assignment', 'label' => "Supprimer une demande d'affectation de policier à une frontière"],
                    ['name' => 'validate-police-officer-assignment', 'label' => "Valider une demande d'affectation de policier à une frontière"],
                    ['name' => 'reject-police-officer-assignment', 'label' => "Rejeter une demande d'affectation de policier à une frontière"],
                ]
            ],
            [
                "name" => "Action",
                "permissions" => [
                    ["name" => "browse-action", "label" => "Consulter la liste des Actions"],
                    ["name" => "store-action", "label" => "Créer une Action"],
                    ["name" => "show-action", "label" => "Consulter une Action"],
                    ["name" => "update-action", "label" => "Mettre à jour une Action"],
                    ["name" => "delete-action", "label" => "Supprimer une Action"],
                ]
            ],
            [
                "name" => "Portefeuille",
                "permissions" => [
                    ['name' => 'show-wallet', 'label' => "Consulter un portefeuille"],
                    ['name' => 'update-wallet', 'label' => "Mettre à jour un portefeuille"],
                ]
            ],
            [
                "name" => "Gage",
                "permissions" => [
                    ['name' => 'browse-pledge', 'label' => "Consulter la liste des gages"],
                    ['name' => 'store-pledge-by-bank', 'label' => "Faire une demande mise en gage par la banque"],
                    ['name' => 'store-pledge-by-distributor', 'label' => "Faire une demande mise en gage par le concessionnaire"],
                    ['name' => 'show-pledge', 'label' => "Consulter un dossier de gage"],
                    ['name' => 'update-pledge', 'label' => "Mettre à jour une demande de gage"],
                    ['name' => 'delete-pledge', 'label' => "Supprimer un gage en cours"],
                    ['name' => 'validate-pledge-by-anatt', 'label' => "Valider un dossier de gage par l'anatt"],
                    ['name' => 'validate-pledge-by-institution', 'label' => "Valider un dossier de gage par une institution"],
                    ['name' => 'validate-pledge-by-justice', 'label' => "Valider un dossier de gage par la justice"],
                    ['name' => 'reject-pledge-by-anatt', 'label' => "Rejeter un gage par l'anatt"],
                    ['name' => 'reject-pledge-by-institution', 'label' => "Rejeter un gage par une institution"],
                    ['name' => 'reject-pledge-by-justice', 'label' => "Rejeter un gage par la justice"],
                    ['name' => 'lift-pledge', 'label' => "Demander la levée d'un gage"],
                ]
            ],
            [
                "name" => "Levée de gage",
                "permissions" => [
                    ['name' => 'browse-pledge-lift', 'label' => "Consulter la liste des demandes de levée de gage"],
                    ['name' => 'show-pledge-lift', 'label' => "Consulter une demande de levée de gage"],
                    ['name' => 'update-pledge-lift', 'label' => "Mettre à jour une demande de levée de gage"],
                    ['name' => 'delete-pledge-lift', 'label' => "Supprimer une demande de levée de gage"],
                    ['name' => 'validate-pledge-lift-by-anatt', 'label' => "Valider un dossier de levée de gage par l'anatt"],
                    ['name' => 'validate-pledge-lift-by-justice', 'label' => "Valider un dossier de  levée de gage par la justice"],
                    ['name' => 'reject-pledge-lift-by-anatt', 'label' => "Rejeter une levée de gage par l'anatt"],
                    ['name' => 'reject-pledge-lift-by-justice', 'label' => "Rejeter une levée de gage par la justice"],
                ]
            ],
            [
                "name" => "Ordre d'impression",
                "permissions" => [
                    ['name' => 'browse-print-order', 'label' => "Consulter les ordres d'impression"],
                    ['name' => 'show-print-order', 'label' => "Consulter un ordre d'impression"],
                    ['name' => 'confirm-print-order-affectation', 'label' => "Confirmer l'affectation d'un ordre d'impression"],
                    ['name' => 'print-plate', 'label' => 'Imprimer la/les plaque(s)'],
                    ['name' => 'print-gray-card', 'label' => 'Imprimer la carte grise'],
                    ['name' => 'confirm-print-reception', 'label' => "Confirmer la réception après impression"],
                    ['name' => 'validate-print', 'label' => "Valider l'impression"],
                ]
            ],
            [
                "name" => "Opposition",
                "permissions" => [
                    ['name' => 'browse-opposition', 'label' => "Consulter la liste des oppositions"],
                    ['name' => 'create-opposition', 'label' => "Consulter la liste des raisons"],
                    ['name' => 'store-opposition', 'label' => "Faire une demande d'opposition"],
                    ['name' => 'show-opposition', 'label' => "Consulter un dossier d'opposition"],
                    ['name' => 'update-opposition', 'label' => "Mettre à jour une opposition"],
                    ['name' => 'delete-opposition', 'label' => "Supprimer un dossier d'opposition"],
                    ['name' => 'validate-opposition-by-clerk', 'label' => "Validater une opposition par le greffier"],
                    ['name' => 'validate-opposition-by-judge', 'label' => "Validater une opposition par le juge d'instruction"],
                    ['name' => 'lift-opposition', 'label' => "Lever une opposition"],
                    ['name' => 'reject-opposition-by-clerk', 'label' => "Rejeter une opposition par le greffier"],
                    ['name' => 'reject-opposition-by-judge', 'label' => "Rejeter une opposition par le juge d'instruction"],
                ]
            ],
            [
                "name" => "Demande de suspension d'espace",
                "permissions" => [
                    ['name' => 'browse-space-suspension-request', 'label' => "Consulter les demandes de suspension d'espace"],
                    ['name' => 'show-space-suspension-request', 'label' => "Consulter une demande de suspension d'espace"],
                    ['name' => 'store-space-suspension-request', 'label' => "Créer une demande de suspension d'espace"],
                    ['name' => 'update-space-suspension-request', 'label' => "Mettre à jour une demande de suspension d'espace"],
                    ['name' => 'validate-space-suspension-request', 'label' => "Valider une demande de suspension d'espace"],
                ]
            ],
            [
                "name" => "Demande de levée de suspension d'espace",
                "permissions" => [
                    ['name' => 'browse-space-suspension-lifting-request', 'label' => "Consulter les demandes de levée de suspension d'espace"],
                    ['name' => 'show-space-suspension-lifting-request', 'label' => "Consulter une demande de levée de suspension d'espace"],
                    ['name' => 'store-space-suspension-lifting-request', 'label' => "Créer une demande de levée de suspension d'espace"],
                    ['name' => 'update-space-suspension-lifting-request', 'label' => "Mettre à jour une demande de levée de suspension d'espace"],
                    ['name' => 'validate-space-suspension-lifting-request', 'label' => "Valider une demande de levée de suspension d'espace"],
                ]
            ],
            [
                "name" => "Personnels",
                "permissions" => [
                    ["name" => "browse-payment-provider", "label" => "Parcourir la liste des aggrégateurs de paiement"],
                    ["name" => "show-payment-provider", "label" => "Consultation d'un membre du personnel"],
                    ["name" => "store-payment-provider", "label" => "Création d'un membre du personnel"],
                    ["name" => "update-payment-provider", "label" => "Mise à jour d'un membre du personnel"],
                    ["name" => "delete-payment-provider", "label" => "Suppression d'un membre du personnel"],
                    ["name" => "search-payment-provider", "label" => "Rechercher un membre du personnel"],
                ]
            ],
            [
                "name" => "Panier",
                "permissions" => [
                    ["name" => "approve-cart", "label" => "Pouvoir approuver le panier"],
                ]
            ],
            /* [
                "name" => "Gestion des demandes",
                "permissions" => [
                    ["name" => "assign-to-center", "label" => "Pouvoir assigner une demande à un centre de gestion"],
                    ["name" => "assign-to-service", "label" => "Pouvoir assigner une demande à un service"],
                    ["name" => "assign-to-staff", "label" => "Pouvoir assigner une demande à un staff"],
                    ["name" => "affect-to-interpol", "label" => "Pouvoir effectuer une demande à interpole"],
                    ["name" => "emit-print-order", "label" => "Pouvoir émettre un ordre d'impression"]
                ]
            ] */
        ];

        foreach ($modules as $item) {
            $module = Module::query()->updateOrCreate(['name' => $item['name']], ['name' => $item['name']]);
            foreach ($item['permissions'] as $value) {
                $value['module_id'] = $module->id;
                $value['guard_name'] = 'api';
                Permission::query()->updateOrCreate(['name' => $value['name']], $value);
            }
        }
    }
}
