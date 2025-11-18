<?php

namespace App\Enums;

use App\Traits\EnumToArray;

Enum Status : string
{
    use EnumToArray;

    case pending = "En attente";
    case validated = "Validé";
    case pre_validated = "Pré validé";
    case rejected = "Rejeté";
    case suspended = "Suspendu";
    case verified = "Vérifié";
    case assigned_to_staff = "Assigné à un agent";
    case assigned_to_service = "Assigné à un service";
    case assigned_to_center = "Assigné à un centre de gestion";
    case active = "Actif";
    case closed = "Clôturé";
    case confirmed = "Confirmé";
    case submitted = "Soumis";
    case approved = "Approuvé";
    case error = "Erroné";
    case canceled = "Annulé";
    case failed = "Échoué";
    case assigned_to_interpol_staff = "Affecté à un agent d'interpol";
    case affected_to_interpol = "Affecté à interpole";
    case rejected_by_interpol = "Rejeté par interpole";
    case pre_rejected_by_interpol = "Pre rejeté par interpole";
    case validated_by_interpol = "Validé par interpole";
    case pre_validated_by_interpol = "Pré-validé par interpole";
    case print_order_emitted = "Ordre d'impression émis";
    case print_order_validated = "Ordre d'impression validée";
    case printing_in_progress = "Impression en cours";
    case printed = "Imprimé";
    case plate_printed = "Plaque imprimée";
    case given = 'Remis';
    case given_to_applicant = "Remis au demandeur";
    case validated_by_anatt = "Validé par l'ANATT";
    case rejected_by_anatt = "Rejeté par l'ANATT";
    case waiting_for_payment = "En attente de paiement";
    case paid = "Payé";
    case in_cart = "Dans le panier";
    case draft = 'Brouillon';
    case alerted = "Alerté";
    case no_reformed = "Non reformé";
    case reformed = "Reformé";
    case done = "Effectué";
    case recorded = "Enregistré";
    case plate_removed = "Plaque retirée";
    case lifting = "Levé";
    case resubmit = "Re-soumettre";
    case resubmitted = "Soumis à nouveau";
    case emitted = "Émis";
    case remitted = "Renvoyé";
    case affected_to_clerk = "Affecté à greffier";
    case old = "Ancien";
    case current = "Actuelle";
    case not_available = "Aucune modification en attente";
    case institution_validated = "Validé par une institution";
    case justice_validated = "Validé par la justice";
    case anatt_validated = "Validé par l'anatt";
    case institution_rejected = "Rejeté par une institution";
    case justice_rejected = "Rejeté par la justice";
    case anatt_rejected = "Rejeté par l'anatt";
    case clerk_validated = "Validé par le greffier";
    case judge_validated = "Validé par le juge d'instruction";
    case clerk_rejected = "Rejeté par le greffier";
    case judge_rejected = "Rejeté par le juge d'instruction";
    case opposition_emitted = "Opposition émise";
    case opposition_lifted_emitted = "Levé d'opposition émise";
    case deactive = "Désactivé";
    case success = "Succès";
}
