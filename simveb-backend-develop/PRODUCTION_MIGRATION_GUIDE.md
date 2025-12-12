# Guide de Migration : Test → Production (Authentification ANIP)

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Fichiers à modifier](#fichiers-à-modifier)
3. [Variables d'environnement](#variables-denvironnement)
4. [Checklist de migration](#checklist-de-migration)
5. [Vérifications post-migration](#vérifications-post-migration)
6. [Rollback en cas de problème](#rollback-en-cas-de-problème)

---

## Vue d'ensemble

En environnement de **test/développement**, SIMVEB utilise plusieurs contournements pour faciliter le développement :

| Fonctionnalité | Mode Test | Mode Production |
|----------------|-----------|-----------------|
| **OTP** | Fixe : `1234` | Aléatoire : 4 chiffres |
| **API ANIP** | Sandbox API (mock) | X-Road (réel) |
| **API DGI** | Sandbox API (mock) | DGI réelle |
| **SMS** | Désactivé | Wirepick/Vonage actif |
| **Email** | Désactivé | Novu actif |
| **SSL Verify** | Désactivé | Activé |
| **NPIs de test** | Liste LOCAL_NPI acceptée | Seulement NPIs réels |
| **IFUs de test** | Liste LOCAL_IFU acceptée | Seulement IFUs réels |
| **Paiements** | Sandbox (FedaPay, KkiaPay) | Production |

---

## Fichiers à modifier

### 1. ❌ **SUPPRIMER ou VIDER : `app/Consts/Utils.php`**

**Problème :** Ce fichier contient des listes de NPIs et IFUs de test qui permettent de bypasser l'authentification réelle.

**Localisation :** `app/Consts/Utils.php:17-81`

**Action requise :**

#### Option A : Vider les listes (Recommandé)
```php
<?php

namespace App\Consts;

use App\Enums\Status;

class Utils
{
    const COMMON_DATE_FORMAT = "D d M Y , H:i";
    const COMMON_DATE_ONLY_FORMAT = "D d M Y";
    const COMMON_TIME_FORMAT = "H:i";

    // ⚠️ PRODUCTION : Vider ces listes pour forcer l'authentification réelle
    const LOCAL_NPI = [];  // ← Vider cette liste
    const LOCAL_IFU = [];  // ← Vider cette liste
}
```

#### Option B : Supprimer complètement les constantes
```php
<?php

namespace App\Consts;

use App\Ensts\Status;

class Utils
{
    const COMMON_DATE_FORMAT = "D d M Y , H:i";
    const COMMON_DATE_ONLY_FORMAT = "D d M Y";
    const COMMON_TIME_FORMAT = "H:i";

    // LOCAL_NPI et LOCAL_IFU supprimés pour la production
}
```

**⚠️ Impact :** Si vous supprimez les constantes, vous devrez aussi modifier tous les fichiers qui les utilisent (voir section suivante).

---

### 2. 🔧 **MODIFIER : `ntech-libs/users-package/src/Services/Auth/OtpService.php`**

**Problème :** OTP fixe à `1234` en environnement de test.

**Localisation :** `ntech-libs/users-package/src/Services/Auth/OtpService.php:15`

**Code actuel :**
```php
$otp = in_array(app()->env, ['local', 'dev', 'development', 'staging']) ||
       in_array($npi, Utils::LOCAL_NPI)
    ? '1234'
    : str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
```

**Code pour la production :**

#### Option 1 : Supprimer complètement la condition
```php
// Production : toujours générer un OTP aléatoire
$otp = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
```

#### Option 2 : Garder la condition mais vider LOCAL_NPI (si Option A choisie plus haut)
```php
// Garde la condition mais LOCAL_NPI est vide en production
$otp = in_array(app()->env, ['local', 'dev', 'development', 'staging']) ||
       in_array($npi, Utils::LOCAL_NPI)
    ? '1234'
    : str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
```

**⚠️ Recommandation :** Utiliser **Option 1** pour plus de clarté.

---

### 3. 🔧 **MODIFIER : `ntech-libs/users-package/src/Services/Auth/OtpService.php`** (suite)

**Problème :** SMS et Email désactivés en environnement de test.

**Localisation :**
- SMS : `ntech-libs/users-package/src/Services/Auth/OtpService.php:35`
- Email : `ntech-libs/users-package/src/Services/Auth/OtpService.php:42`

**Code actuel (SMS) :**
```php
if ($canal == 'sms' && !in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
    $message = $smsPurpose ?? 'Pour poursuivre l\'enregistrement de votre entreprise ';
    $message .= $otp;
    (new SmsService)->send($telephone, $message);
}
```

**Code pour la production (SMS) :**
```php
// Production : toujours envoyer SMS (sauf si canal est email uniquement)
if ($canal == 'sms') {
    $message = $smsPurpose ?? 'Pour poursuivre l\'enregistrement de votre entreprise ';
    $message .= $otp;
    (new SmsService)->send($telephone, $message);
}
```

**Code actuel (Email) :**
```php
if (!in_array(app()->env, ['local', 'dev', 'development', 'staging'])) {
    sendMail(
        $email,
        null,
        NotificationNames::OTP_VERIFICATION,
        $notifData
    );
}
```

**Code pour la production (Email) :**
```php
// Production : toujours envoyer email
sendMail(
    $email,
    null,
    NotificationNames::OTP_VERIFICATION,
    $notifData
);
```

---

### 4. 🔧 **MODIFIER : `app/Services/IdentityService.php`**

**Problème :** Utilise Sandbox API au lieu d'ANIP réel en mode dev.

**Localisation :** Lignes 46-49, 75-78, 170-173, 237-240

**Code actuel :**
```php
if (app()->environment('local') || app()->environment('dev') || in_array($npi, Utils::LOCAL_NPI)) {
    $response = Http::get(config('app.sandbox_host') . '/persons/' . $npi)->json();
} else {
    $response = (new AnipService)->getPerson($npi);
}
```

**Code pour la production :**

#### Option 1 : Supprimer complètement la condition
```php
// Production : toujours utiliser ANIP réel
$response = (new AnipService)->getPerson($npi);
```

#### Option 2 : Garder un fallback sandbox si LOCAL_NPI vide
```php
// Si LOCAL_NPI est vide, cette condition ne s'activera jamais
if (in_array($npi, Utils::LOCAL_NPI)) {
    $response = Http::get(config('app.sandbox_host') . '/persons/' . $npi)->json();
} else {
    $response = (new AnipService)->getPerson($npi);
}
```

**⚠️ Recommandation :** Utiliser **Option 1** pour forcer l'utilisation d'ANIP.

**Fichiers à modifier :**
- `app/Services/IdentityService.php:46-49` (méthode `showByNpi`)
- `app/Services/IdentityService.php:75-78` (méthode `getIdentityByNpi`)
- `app/Services/IdentityService.php:111-114` (méthode `getIdentityByIfu`)
- `app/Services/IdentityService.php:152-155` (méthode `showByIfu`)
- `app/Services/IdentityService.php:170-173` (méthode `storeByNpi`)
- `app/Services/IdentityService.php:237-240` (méthode `storeByIfu`)

---

### 5. 🔧 **MODIFIER : `app/Services/External/AnipService.php`**

**Problème :** Vérification SSL désactivée.

**Localisation :** `app/Services/External/AnipService.php:20`

**Code actuel :**
```php
public function __construct()
{
    $this->httpClient = new Client(['verify' => false]);
}
```

**Code pour la production :**
```php
public function __construct()
{
    // Production : activer la vérification SSL
    $this->httpClient = new Client([
        'verify' => true,  // ou path vers CA bundle : '/path/to/ca-bundle.crt'
        'timeout' => 30,   // Timeout de 30 secondes
        'connect_timeout' => 10,  // Timeout de connexion
    ]);
}
```

**Avec certificat personnalisé (si nécessaire) :**
```php
public function __construct()
{
    $config = [
        'verify' => env('XROAD_SSL_VERIFY', true),
        'timeout' => 30,
        'connect_timeout' => 10,
    ];

    // Si certificat client requis pour X-Road
    if (env('XROAD_CLIENT_CERT')) {
        $config['cert'] = [
            env('XROAD_CLIENT_CERT'),
            env('XROAD_CLIENT_CERT_PASSWORD', '')
        ];
    }

    // Si clé privée requise
    if (env('XROAD_CLIENT_KEY')) {
        $config['ssl_key'] = [
            env('XROAD_CLIENT_KEY'),
            env('XROAD_CLIENT_KEY_PASSWORD', '')
        ];
    }

    $this->httpClient = new Client($config);
}
```

---

### 6. 🔧 **MODIFIER : Seeders (si utilisés en production)**

**Problème :** Certains seeders utilisent des données de test.

**Fichiers concernés :**
- `database/seeders/Staff/AnattAdminSeeder.php:31,50`
- `database/seeders/ApprovedSeeder.php:28,31`

**⚠️ Recommandation :** Ne PAS exécuter ces seeders en production, ou les modifier pour utiliser des données réelles.

---

### 7. 🔧 **MODIFIER : Services de paiement**

**Problème :** Services de paiement en mode sandbox.

**Fichiers concernés :**
- FedaPay : `config/config.php:5-7`
- KkiaPay : `config/config.php:9-12`

**Configuration actuelle :**
```php
'fedapay_pk' => env('FEDAPAY_PK', "pk_sandbox_mmWicFggMFQDW8qdrGmRBV9A"),
'fedapay_sk' => env('FEDAPAY_SK', "sk_sandbox_y7plFA3lQHh7zfHEYeD8ImPR"),
'fedapay_env' => env('FEDAPAY_ENV', "sandbox"),

'kkiapay_pk' => env('KKIAPAY_PK', "5766c4e0824211efb2cd736c2a0bab43"),
'kkiapay_sk' => env('KKIAPAY_SK', "tpk_5766ebf1824211efb2cd736c2a0bab43"),
'kkiapay_sand' => env('KKIAPAY_ENV', "sandbox"),
```

**⚠️ Action requise :** Configurer les clés de production dans `.env` (voir section Variables d'environnement).

---

## Variables d'environnement

### Fichier `.env` - Modifications requises

```bash
# ============================================
# ENVIRONNEMENT
# ============================================
APP_ENV=production  # ← CRITIQUE : Changer de 'local' ou 'dev' à 'production'
APP_DEBUG=false     # ← CRITIQUE : Désactiver le debug en production

# ============================================
# X-ROAD & ANIP (PRODUCTION)
# ============================================
XROAD_BASE_URL=https://xroad-production.gouv.bj/r1/BJ/GOV/ANATT/SIMVEB
XROAD_SSL_VERIFY=true

# Optionnel : Si certificat client requis
XROAD_CLIENT_CERT=/path/to/client-cert.pem
XROAD_CLIENT_CERT_PASSWORD=your_cert_password
XROAD_CLIENT_KEY=/path/to/client-key.pem
XROAD_CLIENT_KEY_PASSWORD=your_key_password

# ============================================
# SANDBOX APIs (À SUPPRIMER EN PRODUCTION)
# ============================================
# SANDBOX_HOST=https://sandbox-api.simveb-bj.com/api  # ← Commenter ou supprimer
# CHECK_NPI_URL=https://sandbox-api.simveb-bj.com/api/persons  # ← Commenter
# CHECK_IFU_URL=https://sandbox-api.simveb-bj.com/api/companies  # ← Commenter

# ============================================
# DGI (PRODUCTION)
# ============================================
DGI_TOKEN=your_production_dgi_token

# ============================================
# SMS (PRODUCTION - WIREPICK)
# ============================================
SMS_DRIVER=wirepick
WIREPICK_HOST=https://apisms.wirepick.com/
WIREPICK_USER=your_production_username
WIREPICK_PASSWORD=your_production_password
WIREPICK_SENDER_ID=SIMVEB

# ============================================
# EMAIL (PRODUCTION - NOVU)
# ============================================
NOVU_API_KEY=your_production_novu_api_key

# ============================================
# PAIEMENTS (PRODUCTION)
# ============================================
# FedaPay
FEDAPAY_PK=your_fedapay_production_public_key  # ← Clé LIVE
FEDAPAY_SK=your_fedapay_production_secret_key  # ← Clé LIVE
FEDAPAY_ENV=live  # ← Passer en 'live'

# KkiaPay
KKIAPAY_PK=your_production_public_key
KKIAPAY_SK=your_production_secret_key
KKIAPAY_ENV=live  # ← Passer en 'live'
KKIAPAY_SEC=your_production_secret
KKIAPAY_SANDBOX=false

# ============================================
# OTP
# ============================================
OTP_DURATION=5  # minutes (peut être ajusté selon les besoins)

# ============================================
# REDIS
# ============================================
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_production_redis_password
REDIS_PORT=6379

# ============================================
# MONITORING & LOGGING
# ============================================
SENTRY_LARAVEL_DSN=your_sentry_dsn  # Pour le monitoring des erreurs
LOG_CHANNEL=stack
LOG_LEVEL=warning  # En production : warning ou error
```

---

## Checklist de migration

### Phase 1 : Préparation (1-2 jours avant)

- [ ] **Backup complet de la base de données**
  ```bash
  mysqldump -u user -p simveb_prod > backup_$(date +%Y%m%d_%H%M%S).sql
  ```

- [ ] **Backup du code actuel**
  ```bash
  git tag -a pre-production-migration-$(date +%Y%m%d) -m "Backup avant migration prod"
  git push origin --tags
  ```

- [ ] **Tester X-Road en staging**
  ```bash
  php artisan test:anip --npi=REAL_NPI
  ```

- [ ] **Vérifier les credentials production**
  - [ ] X-Road : URL et certificats
  - [ ] DGI : Token
  - [ ] Wirepick : Username/Password
  - [ ] Novu : API Key
  - [ ] FedaPay : Clés live
  - [ ] KkiaPay : Clés live

- [ ] **Préparer un plan de rollback**

---

### Phase 2 : Modifications du code

- [ ] **1. Vider `app/Consts/Utils.php`**
  ```bash
  nano app/Consts/Utils.php
  # Vider LOCAL_NPI et LOCAL_IFU
  ```

- [ ] **2. Modifier `OtpService.php`**
  - [ ] Ligne 15 : Supprimer OTP fixe
  - [ ] Ligne 35 : Activer envoi SMS
  - [ ] Ligne 42 : Activer envoi Email

- [ ] **3. Modifier `IdentityService.php`**
  - [ ] Ligne 46-49 : Forcer ANIP
  - [ ] Ligne 75-78 : Forcer ANIP
  - [ ] Ligne 111-114 : Forcer DGI
  - [ ] Ligne 152-155 : Forcer DGI
  - [ ] Ligne 170-173 : Forcer ANIP
  - [ ] Ligne 237-240 : Forcer DGI

- [ ] **4. Modifier `AnipService.php`**
  - [ ] Ligne 20 : Activer SSL verify

- [ ] **5. Commiter les changements**
  ```bash
  git add .
  git commit -m "Migrate to production: Remove test bypasses and enable real services"
  ```

---

### Phase 3 : Configuration environnement

- [ ] **1. Copier `.env.production` vers `.env`**
  ```bash
  cp .env.production .env
  ```

- [ ] **2. Éditer `.env`**
  ```bash
  nano .env
  ```
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `XROAD_BASE_URL=...`
  - [ ] Toutes les clés API production

- [ ] **3. Régénérer les caches**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

- [ ] **4. Optimiser l'autoload**
  ```bash
  composer install --no-dev --optimize-autoloader
  ```

---

### Phase 4 : Vérifications

- [ ] **1. Test ANIP avec NPI réel**
  ```bash
  php artisan test:anip --npi=REAL_NPI
  ```

- [ ] **2. Test authentification complète**
  - [ ] Envoyer OTP avec un vrai NPI
  - [ ] Vérifier réception SMS
  - [ ] Vérifier réception Email
  - [ ] Valider OTP (doit être aléatoire, pas '1234')
  - [ ] Vérifier génération token OAuth2

- [ ] **3. Test inscription**
  - [ ] Nouveau NPI
  - [ ] Vérification ANIP
  - [ ] OTP aléatoire
  - [ ] Création compte

- [ ] **4. Vérifier les logs**
  ```bash
  tail -f storage/logs/laravel.log
  tail -f storage/logs/anip.log
  ```

- [ ] **5. Test paiement (montant minimal)**
  - [ ] FedaPay : Transaction test
  - [ ] KkiaPay : Transaction test

---

### Phase 5 : Monitoring (première semaine)

- [ ] **Surveiller les métriques**
  - [ ] Taux de succès ANIP > 95%
  - [ ] Temps de réponse ANIP < 3s
  - [ ] Taux de succès OTP > 90%
  - [ ] Taux de livraison SMS > 95%

- [ ] **Vérifier les erreurs**
  ```bash
  # Erreurs ANIP
  grep "ANIP ERROR" storage/logs/anip.log

  # Erreurs générales
  grep "ERROR" storage/logs/laravel.log
  ```

- [ ] **Alertes à configurer**
  - [ ] Spike d'erreurs ANIP
  - [ ] Échecs OTP répétés
  - [ ] Temps de réponse dégradé

---

## Vérifications post-migration

### Tests critiques à effectuer

#### 1. Test d'authentification complète

```bash
# Test avec Postman ou curl

# 1. Envoyer OTP
curl -X POST https://api.simveb-bj.com/api/login/send-otp \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"npi": "REAL_NPI"}'

# Réponse attendue :
# {
#   "success": true,
#   "data": {
#     "npi": "XXXXXXXXXX",
#     "telephone": "***4856",
#     "otp_duration": 5,
#     "message": "Code OTP envoyé..."
#   }
# }

# 2. Vérifier réception SMS (téléphone réel)
# OTP doit être différent de 1234

# 3. Valider OTP
curl -X POST https://api.simveb-bj.com/api/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=password&client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&username=REAL_NPI&password=RECEIVED_OTP"

# Réponse attendue :
# {
#   "success": true,
#   "data": {
#     "access_token": "...",
#     "token_type": "Bearer",
#     "expires_in": 31536000
#   }
# }
```

#### 2. Vérifier qu'un NPI de test ne fonctionne plus

```bash
# Test avec un NPI de la liste LOCAL_NPI (ex: 8765432101)
curl -X POST https://api.simveb-bj.com/api/login/send-otp \
  -H "Content-Type: application/json" \
  -d '{"npi": "8765432101"}'

# Réponse attendue : ERREUR 404
# {
#   "success": false,
#   "message": "Ce NPI n'est pas reconnu."
# }
```

#### 3. Vérifier l'appel ANIP réel

```bash
# Vérifier les logs
tail -f storage/logs/anip.log

# On doit voir :
# [2025-12-12 10:30:45] ANIP Request Started {"npi":"XXXXXXXXXX","ip":"xxx.xxx.xxx.xxx"}
# [2025-12-12 10:30:46] SOAP Response {"status":200,"body":"..."}
```

#### 4. Vérifier les paiements

```bash
# Test FedaPay (montant minimal : 100 FCFA)
# Dans l'interface SIMVEB, effectuer une transaction test

# Vérifier dans le dashboard FedaPay :
# - Transaction en mode LIVE
# - Webhook reçu
# - Statut : succeeded

# Idem pour KkiaPay
```

---

## Rollback en cas de problème

Si des problèmes critiques surviennent, suivez cette procédure :

### 1. Rollback code

```bash
# Retourner au tag pré-migration
git checkout pre-production-migration-YYYYMMDD

# Ou annuler le dernier commit
git revert HEAD
git push origin main
```

### 2. Rollback configuration

```bash
# Restaurer l'ancien .env
cp .env.backup .env

# Recréer les caches
php artisan config:cache
php artisan route:cache
```

### 3. Rollback base de données (si nécessaire)

```bash
# Restaurer le backup
mysql -u user -p simveb_prod < backup_YYYYMMDD_HHMMSS.sql
```

### 4. Vérifier le retour en arrière

```bash
# Test avec NPI de test (doit fonctionner)
php artisan test:anip --npi=8765432101

# OTP doit être '1234'
```

---

## Résumé des fichiers modifiés

| Fichier | Action | Criticité |
|---------|--------|-----------|
| `app/Consts/Utils.php` | Vider LOCAL_NPI et LOCAL_IFU | 🔴 CRITIQUE |
| `ntech-libs/users-package/src/Services/Auth/OtpService.php` | Supprimer OTP fixe + activer SMS/Email | 🔴 CRITIQUE |
| `app/Services/IdentityService.php` | Forcer ANIP/DGI réels | 🔴 CRITIQUE |
| `app/Services/External/AnipService.php` | Activer SSL verify | 🟡 IMPORTANT |
| `.env` | Toutes les variables | 🔴 CRITIQUE |

---

## Support et contacts

**En cas de problème :**

1. **Équipe SIMVEB** : support@simveb-bj.com
2. **ANIP (X-Road)** : support-xroad@anip.bj
3. **DGI** : support-dgi@impots.bj
4. **Wirepick (SMS)** : support@wirepick.com
5. **Novu (Email)** : support@novu.co

---

## Annexe : Différences Test vs Production

### Tableau récapitulatif

| Composant | Test/Dev | Production |
|-----------|----------|------------|
| **APP_ENV** | local, dev, staging | production |
| **APP_DEBUG** | true | false |
| **OTP** | 1234 (fixe) | Aléatoire 4 chiffres |
| **NPI valides** | LOCAL_NPI[] + ANIP | ANIP uniquement |
| **IFU valides** | LOCAL_IFU[] + DGI | DGI uniquement |
| **API ANIP** | Sandbox mock | X-Road réel |
| **API DGI** | Sandbox mock | DGI réelle |
| **SSL Verify** | false | true |
| **SMS** | Désactivé | Wirepick actif |
| **Email** | Désactivé | Novu actif |
| **FedaPay** | pk_sandbox_... | pk_live_... |
| **KkiaPay** | sandbox | live |
| **Logs** | debug | warning/error |

---

**Version du document :** 1.0
**Date de création :** 2025-12-12
**Auteur :** Équipe Technique SIMVEB
**Dernière mise à jour :** 2025-12-12
