<?php

namespace Database\Seeders\Notifications;

use App\Consts\NotificationNames;

class Duplicate
{

    const  NOTIFICATION_CONFIGS = [
            [
                'name' => NotificationNames::PLATE_DUPLICATE_SUBMITTED,
                'title' => 'Dossier soumis',
                'message_sms' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
                'message_in_app' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
                'message_mail' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
                'total_repetition_count' => 1,
                'frequency_in_days' => 7,
            ],
        [
            'name' => NotificationNames::GRAY_CARD_DUPLICATE_SUBMITTED,
            'title' => 'Dossier soumis',
            'message_sms' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'message_in_app' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'message_mail' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'total_repetition_count' => 1,
            'frequency_in_days' => 7,
        ],
        [
            'name' => NotificationNames::PLATE_DUPLICATE_VERIFIED,
            'title' => 'Dossier soumis',
            'message_sms' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'message_in_app' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'message_mail' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'total_repetition_count' => 1,
            'frequency_in_days' => 7,
        ],
        [
            'name' => NotificationNames::GRAY_CARD_DUPLICATE_VALIDATED,
            'title' => 'Dossier soumis',
            'message_sms' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'message_in_app' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'message_mail' => 'Votre dossier de changement de la couleur de votre plaque d\'immatriculation a été soumis avec succès.',
            'total_repetition_count' => 1,
            'frequency_in_days' => 7,
        ],

    ];
}
