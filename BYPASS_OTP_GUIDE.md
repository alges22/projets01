# Guide de Test SIMVEB - Bypass OTP pour Développement

**⚠️ ENVIRONNEMENT DE DÉVELOPPEMENT/TEST UNIQUEMENT**

Ce guide explique comment tester le système SIMVEB sans avoir à recevoir de vrais codes OTP par SMS/Email.

---

## 🎯 Solutions pour Bypasser l'OTP

### ✅ Solution 1 : Utiliser le Code OTP Universel (RECOMMANDÉ)

Le système a déjà un code OTP **hardcodé en développement**.

**Code OTP universel** : `1234`

#### Configuration requise

Dans le fichier `.env` du backend, assurez-vous d'avoir :

```env
APP_ENV=local
# OU
APP_ENV=dev
# OU
APP_ENV=development
# OU
APP_ENV=staging
```

#### Comment ça marche ?

**Fichier** : `simveb-backend-develop/ntech-libs/users-package/src/Services/Auth/OtpService.php:15`

```php
$otp = in_array(app()->env, ['local', 'dev', 'development', 'staging'])
       || in_array($npi, Utils::LOCAL_NPI)
       ? '1234'  // Code fixe en dev
       : str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT); // Code aléatoire en prod
```

**Si l'environnement est `local`, `dev`, `development` ou `staging`, le code OTP sera toujours `1234`**

#### Test rapide

1. Allez sur la page de connexion
2. Entrez un NPI de test (ex: `8765432101`)
3. Cliquez sur "Envoyer OTP"
4. Entrez le code : **`1234`**
5. Vous êtes connecté ! ✅

---

## 👥 Comptes de Test Pré-Configurés

### Super Admin (Accès Total)

Utilisez ce compte pour tester toutes les fonctionnalités :

```
NPI (Username):  8765432101
Code OTP:        1234
Mot de passe:    here is the pass

Email:           nautilustest@mail.com
Téléphone:       +22951104856
```

**Profils disponibles avec ce compte :**
- ✅ User (Citoyen)
- ✅ ANATT (Admin)
- ✅ Police
- ✅ Central Garage
- ✅ GMA
- ✅ GMD
- ✅ Auctioneer
- ✅ Bank
- ✅ Court
- ✅ Distributor
- ✅ Interpol
- ✅ Affiliate

### Autres Comptes de Test

| Profil | NPI | Email | Code OTP |
|--------|-----|-------|----------|
| **Citoyen** | 2109876540 | wwilliam@gmail.com | 1234 |
| **Police** | 9876543210 | isabella@example.com | 1234 |
| **Interpol** | 1632101074 | glory@example.com | 1234 |
| **Banque** | 8823456789 | bankeradmin@simveb.bj | 1234 |
| **Garage** | 3610987650 | mia@example.com | 1234 |
| **GMA** | 9987654320 | ava@example.com | 1234 |
| **GMD** | 9826543210 | michael@example.com | 1234 |
| **Commissaire** | 4321998760 | benjamin@example.com | 1234 |
| **Distributeur** | 1098765430 | contact@lesbagnoles.com | 1234 |
| **Affilié** | 7109876540 | alexander@example.com | 1234 |

**Note** : Tous les comptes utilisent le code OTP **`1234`** en environnement de développement.

---

## 🔑 Solution 2 : NPIs Whitelistés

Le système a une liste de NPIs qui utilisent **TOUJOURS** le code `1234`, même en production.

**Fichier** : `simveb-backend-develop/app/Consts/Utils.php`

### Liste des NPIs Whitelistés (52 NPIs)

```php
const LOCAL_NPI = [
    "8765432101",  // Super Admin
    "8765434101",
    "7654321090",
    "6543210980",
    "5432109870",
    "4321098760",
    "3210987650",
    "1098765430",  // Distributeur
    "0987654320",
    "9876543210",  // Police
    "8765432100",
    "7054321090",
    "0543210980",
    "5632109870",
    "4321998760",  // Commissaire
    "3610987650",  // Garage
    "7109876540",  // Affilié
    "9987654320",  // GMA
    "9826543210",  // GMD
    "0065432101",
    "1632101074",  // Interpol + ANATT Staff
    "1632111074",
    "2632101074",
    "1632101574",
    "1636101074",
    "1932101074",
    "1632221074",
    "1632101000",
    "1644101074",
    "1653202001",
    "1665103002",
    "1672304003",
    "1684505004",
    "1696706005",
    "1747731010",
    "1708907006",
    "1711118007",
    "1723329008",
    "1735530009",
    "1759932011",
    "1772133012",
    "1784334013",
    "1796535014",
    "1808736015",
    "1820937016",
    "1833138017",
    "1845339018",
    "1857540019",
    "1871741020",
    "1883942021"
];
```

### Avantage

Ces NPIs fonctionnent avec le code `1234` **dans TOUS les environnements** (même en production si besoin de test).

### Pour ajouter un nouveau NPI de test

Éditez le fichier `simveb-backend-develop/app/Consts/Utils.php` et ajoutez votre NPI à la liste :

```php
const LOCAL_NPI = [
    "8765432101",
    "YOUR_NEW_NPI_HERE",  // Ajoutez ici
    // ... reste de la liste
];
```

---

## 📝 Solution 3 : Créer un Nouveau Compte de Test

### Étape 1 : Ajouter le NPI à la whitelist

Éditez `app/Consts/Utils.php` et ajoutez votre NPI :

```php
const LOCAL_NPI = [
    "8765432101",
    "1234567890",  // Votre nouveau NPI
    // ...
];
```

### Étape 2 : Créer un seeder

Créez un fichier `database/seeders/MyTestUserSeeder.php` :

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account\User;
use App\Models\Auth\Profile;
use Ntech\IdentityPackage\Models\Identity;
use Illuminate\Support\Facades\Hash;

class MyTestUserSeeder extends Seeder
{
    public function run()
    {
        $identity = Identity::create([
            'npi' => '1234567890',
            'lastname' => 'TEST',
            'firstname' => 'User',
            'birthdate' => '1990-01-01',
            'telephone' => '+22912345678',
            'email' => 'test@example.com',
            'state_id' => 1,
            'town_id' => 1,
            'district_id' => 1,
            'village_id' => 1,
            'house' => 'Test House'
        ]);

        $user = User::create([
            'username' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('here is the pass'),
            'identity_id' => $identity->id
        ]);

        $profileType = \App\Models\Auth\ProfileType::where('code', 'user')->first();

        $profile = Profile::create([
            'user_id' => $user->id,
            'type_id' => $profileType->id,
            'identity_id' => $identity->id
        ]);

        $user->update(['online_profile_id' => $profile->id]);
    }
}
```

### Étape 3 : Exécuter le seeder

```bash
docker-compose exec app php artisan db:seed --class=MyTestUserSeeder
```

### Étape 4 : Se connecter

```
NPI:        1234567890
Code OTP:   1234
```

---

## 🚀 Solution 4 : Seed Database Complète

Pour avoir TOUS les comptes de test en une commande :

```bash
# Reset et seed complet
docker-compose exec app php artisan migrate:fresh --seed

# OU juste seed (sans reset)
docker-compose exec app php artisan db:seed
```

Cette commande crée automatiquement :
- 1 Super Admin (tous les profils)
- 1 Citoyen simple
- 2 Staff ANATT
- 1 Police
- 1 Interpol
- 1 Central Garage
- 1 GMA
- 1 GMD
- 1 Commissaire
- 1 Banque
- 1 Distributeur
- 1 Affilié

**Tous avec le code OTP `1234`** en environnement de développement.

---

## 🔧 Configuration Backend

### Vérifier votre .env

```bash
cd simveb-backend-develop
cat .env | grep APP_ENV
```

Devrait retourner :
```
APP_ENV=local
```

Ou :
```
APP_ENV=dev
```

### Si vous devez changer l'environnement

```bash
cd simveb-backend-develop
nano .env
```

Changez :
```env
APP_ENV=production
```

En :
```env
APP_ENV=local
```

Puis redémarrez :
```bash
docker-compose restart app
```

---

## 📱 Test du Flow Complet

### 1. Connexion Simple

**Portal** : http://localhost:8003/auth/login

1. Entrez le NPI : `8765432101`
2. Cliquez "Continuer"
3. Entrez le code OTP : `1234`
4. Cliquez "Se connecter"
5. ✅ Vous êtes connecté !

### 2. Inscription Nouvelle Personne

**Portal** : http://localhost:8003/auth/register/personne

**Important** : Pour l'inscription, vous devez utiliser un NPI qui :
- N'existe PAS encore en base
- EST dans la whitelist `Utils::LOCAL_NPI`

**Exemple** :

1. Ajoutez un nouveau NPI dans `Utils.php` :
```php
const LOCAL_NPI = [
    "8765432101",
    "1111111111",  // Nouveau NPI pour test inscription
    // ...
];
```

2. Sur le portal :
   - Email : `newuser@test.com`
   - NPI : `1111111111`
   - Code OTP : `1234`
   - Remplissez l'adresse
   - Validez

3. Maintenant vous pouvez vous connecter avec :
   - NPI : `1111111111`
   - Code OTP : `1234`

### 3. Inscription Entreprise

**Portal** : http://localhost:8003/auth/register/entreprise

**IFUs whitelistés** (`Utils::LOCAL_IFU`) :

```php
const LOCAL_IFU = [
    "1234567890123",
    "2345678901234",
    "3456789012345",
    "4567890123456",
    "5678901234567",
    "6789012345678",
    "7890123456789",
    "8790120456789",
    "9012345678901",
    "0123456789012"
];
```

1. Saisissez un IFU : `1234567890123`
2. Code OTP reçu par email : `1234`
3. NPI du premier admin : `8765432101` (ou un autre de la whitelist)
4. Uploadez des documents (PDF/images)
5. Validez

**⚠️ Note** : L'entreprise doit être validée par un admin dans le backoffice avant de pouvoir se connecter.

---

## 🎨 Test Multi-Profils (Affiliate)

Pour tester l'application Affiliate avec différents profils :

### 1. Connexion comme Super Admin

**URL** : https://localhost:5173

```
NPI:        8765432101
Code OTP:   1234
```

### 2. Changement de Profil

Une fois connecté, vous verrez un menu déroulant avec tous vos profils :
- User
- ANATT
- Police
- Central Garage
- GMA
- GMD
- Bank
- Court
- Distributor
- Interpol
- Affiliate

Sélectionnez le profil souhaité pour tester les fonctionnalités spécifiques.

---

## 🐛 Dépannage

### Problème : OTP non accepté

**Solution 1** : Vérifier l'environnement
```bash
docker-compose exec app php artisan tinker
>>> app()->environment()
=> "local"  # Doit être local, dev, development ou staging
```

**Solution 2** : Vérifier que le NPI est dans la whitelist
```bash
docker-compose exec app php artisan tinker
>>> \App\Consts\Utils::LOCAL_NPI
=> [
     "8765432101",
     ...
   ]
```

**Solution 3** : Clear cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose restart
```

### Problème : Compte n'existe pas

**Solution** : Seed la database
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Problème : SMS/Email non reçus en local

**C'est normal !** En environnement local/dev, les SMS et emails ne sont **PAS** envoyés.

Voir le code dans `OtpService.php:35-49` :

```php
if ($canal == 'sms' && !in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
    // SMS envoyé uniquement en production
    (new SmsService)->send($telephone, $message);
}

if (!in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
    // Email envoyé uniquement en production
    sendMail($email, null, NotificationNames::OTP_VERIFICATION, $notifData);
}
```

**Vous n'avez PAS besoin de recevoir de SMS/Email**, utilisez simplement le code `1234`.

---

## 📋 Checklist de Test

### Pour tester la connexion

- [ ] `.env` contient `APP_ENV=local`
- [ ] Docker containers sont lancés : `docker-compose ps`
- [ ] Database est seedée : `docker-compose exec app php artisan db:seed`
- [ ] Utiliser un NPI de test : `8765432101`
- [ ] Utiliser le code OTP : `1234`

### Pour tester l'inscription

- [ ] Ajouter un nouveau NPI dans `Utils::LOCAL_NPI`
- [ ] Utiliser un email unique
- [ ] Utiliser le code OTP : `1234`
- [ ] Remplir l'adresse complète

### Pour tester l'entreprise

- [ ] Utiliser un IFU de test : `1234567890123`
- [ ] Code OTP : `1234`
- [ ] NPI admin whitelisté : `8765432101`
- [ ] Valider dans le backoffice

---

## 🔒 Sécurité - Points Importants

### ⚠️ ATTENTION

1. **Jamais en production** : Le code `1234` ne doit **JAMAIS** être utilisé en production
2. **Whitelist limitée** : Ne pas ajouter de vrais NPIs dans `Utils::LOCAL_NPI` en production
3. **Environnement** : Toujours vérifier `APP_ENV` avant de déployer

### Configuration Production

En production, le système :
- Génère des codes OTP **aléatoires** de 4 chiffres
- Envoie de **vrais SMS** via Vonage
- Envoie de **vrais Emails**
- Les NPIs whitelistés continuent d'utiliser `1234` (à nettoyer avant prod)

**Avant de déployer en production** :

```bash
# 1. Nettoyer la whitelist
# Éditer app/Consts/Utils.php et réduire LOCAL_NPI à un minimum

# 2. Changer l'environnement
APP_ENV=production

# 3. Vérifier les configurations
APP_DEBUG=false
```

---

## 📚 Résumé

### Code OTP Universel de Dev

```
1234
```

### Compte de Test Principal

```
NPI:        8765432101
Code OTP:   1234
Email:      nautilustest@mail.com
```

### Commande de Reset Complète

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Vérification Rapide

```bash
# Check environnement
docker-compose exec app php artisan tinker
>>> app()->environment()
=> "local"

# Check whitelist
>>> \App\Consts\Utils::LOCAL_NPI
```

---

## 💡 Conseil Final

Pour un test rapide sans configuration :

1. ✅ Utilisez `APP_ENV=local` dans `.env`
2. ✅ Seed la database : `docker-compose exec app php artisan db:seed`
3. ✅ Connectez-vous avec NPI `8765432101` et OTP `1234`
4. ✅ C'est tout ! 🎉

---

**Document créé le** : 2025-12-08
**Version** : 1.0
**Projet** : SIMVEB - Guide de Test OTP
