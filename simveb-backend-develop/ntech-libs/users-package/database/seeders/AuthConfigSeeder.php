<?php

namespace Ntech\UserPackage\Database\Seeders;

use Ntech\MetadataPackage\Enums\MetaDataKeys;
use Ntech\MetadataPackage\Models\MetaData;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class AuthConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authSettings = [
            ["key" => 'inactivity_control', "value" => false, "label" => "Contrôle du temps d'inactivité"],
            ["key" => 'inactivity_time_limit', "value" => 15, "label" => "Nombre de jour d'inactivité"], //in days
            ["key" => 'password_expiration_control', "value" => false, "label" => "Contrôle de la réinitialisation du mot de passe"],
            ["key" => 'password_lifetime', "value" => 90,  "label" => "Nombre de jours de vie du mdp"], //in days
            ["key" => 'max_password_histories', "value" => 12, "label" => "Nombre d'historique de mot de passe"],
            ["key" => 'password_notif_delay', "value" => 14,  "label" => "Délaie pour notifier de changer de mot de passe"],
            ["key" => 'password_notif_msg',
                "value" => "Votre mot de passe arrive à expiration dans 14 jours, pensez à le renouveler.",
                "label" => "Message de notifications de mise à jour du mdp"
            ],
            ["key" => 'password_expiration_msg', "value" => "Votre mot de passe a expiré", "label" => "Message de notification de l'expiration du mdp"],
            ["key" => 'block_attempt_control', "value" => false, "label" => "Contrôle du blocage des tentatives"],
            ["key" => 'max_attempt', "value" => 3, "label" => "Nombre maximum de tentative"],
            ["key" => 'attempt_delay', "value" => 15,  "label" => "Délaie d'attente entre les tentative"], //in minutes
            ["key" => 'attempt_waiting_time', "value" => 60, "label" => "Temps d'attente après blocage"], //15 minutes
            ["key" => 'account_blocked_msg',
                "value" => "Votre compte a été bloqué suite à de nombreuses tentative manqué", "label" => "Message de notification de compte bloqué"],
            ["key" => 'inactive_account_msg', "value" => "Votre compte a été désactivé pour inactivité", "label" => "Message de notification de compte inactif"],
            ['key' => MetaDataKeys::password_expiration_control->name, 'value' => false, 'label' => "Controle de l'expiration du mot de passe"],
            ['key' => MetaDataKeys::max_password_histories->name, 'value' => 10, 'label' => "Nombre maximum de mot de passe"],
            ['key' => MetaDataKeys::otp_duration->name, 'value' => 5, 'label' => "Durée de validité du OTP"],
        ];

            Metadata::query()->updateOrCreate([
                "name"=> MetaDataKeys::auth_params->name
            ],[
                'id' => (string)Str::uuid(),
                'name' => MetaDataKeys::auth_params->name,
                "label" => "Paramètre d'authentification",
                'data' => $authSettings
            ]);



    }
}
