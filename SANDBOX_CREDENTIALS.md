# 🔐 Variables et Comptes Test SIMVEB - Environnement Sandbox

**Date de création**: 2025-11-25
**Projet**: SIMVEB (Système Intégré de Gestion des Véhicules du Bénin)
**Environnement**: Sandbox / Test / Development

---

## 📋 Table des Matières

1. [Variables d'Environnement Backend](#variables-denvironnement-backend)
2. [Variables d'Environnement Frontend](#variables-denvironnement-frontend)
3. [Comptes Utilisateurs Test](#comptes-utilisateurs-test)
4. [API Tiers - Credentials Sandbox](#api-tiers---credentials-sandbox)
5. [Base de Données](#base-de-données)
6. [Services d'Infrastructure](#services-dinfrastructure)
7. [URLs des Services](#urls-des-services)

---

## 🔧 Variables d'Environnement Backend

### Configuration Laravel (.env)

```env
# Application
APP_NAME="SimVeb"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
PORTAL_URL=http://localhost:8003

# Base de Données PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=simveb
DB_USERNAME=simveb
DB_PASSWORD=password

# Mail (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=finplex.ntech@gmail.com
MAIL_PASSWORD=lofeslljqmuvdqal
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@simveb.bj"
MAIL_FROM_NAME="${APP_NAME}"

# FedaPay (Paiement Mobile)
FEDAPAY_PUBLIC_KEY=pk_sandbox_cb7jY3KvZ_FLNFp_LfqenqZA
FEDAPAY_SECRET_KEY=sk_sandbox_WvbsARrZRcSxWd4ty8yn_3cj
FEDAPAY_ENVIRONMENT=sandbox

# KKiaPay (Paiement Mobile)
KKIAPAY_PUBLIC_KEY=5766c4e0824211efb2cd736c2a0bab43
KKIAPAY_SECRET_KEY=tpk_5766ebf1824211efb2cd736c2a0bab43
KKIAPAY_SANDBOX=true
KKIAPAY_SECRET=tsk_5766ebf2824211efb2cd736c2a0bab43

# Novu (Notifications)
NOVU_SECRET_KEY=6dbc7c10270af82ccde1212b96a5d1c8
NOVU_PUBLIC_KEY=fBQFolGUuKeG
NOVU_ENV_ID=66843a0f8d5e505deeca014d

# Sentry (Monitoring d'Erreurs)
SENTRY_LARAVEL_DSN=https://3c88e663578f3b27845c0cb554fdb51d@o4505782362177536.ingest.sentry.io/4505782364864512
SENTRY_TRACES_SAMPLE_RATE=1.0

# APIs Gouvernementales Sandbox
SANDBOX_HOST=https://sandbox-api.simveb-bj.com/api/
CHECK_NPI_URL=https://sandbox-api.simveb-bj.com/api/persons
CHECK_IFU_URL=https://sandbox-api.simveb-bj.com/api/companies
DOUANE_API=https://sandbox-api.simveb-bj.com/api/

# X-Road (Intégration Gouvernementale)
XROAD_BASE_URL=https://common-ss.xroad.bj:8443
ANIP_BASE_URL=
DGI_BASE_URL=
DOUANE_BASE_URL=

# Token DGI (Direction Générale des Impôts)
DGI_TOKEN=eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI1MDQiLCJVc2VyIjp7ImlkIjo1MDQsIm5vbSI6IkFOQVRUIEJFTklOIiwicHJlbm9tIjoiQU5BVFQgQkVOSU4iLCJ1c2VybmFtZSI6ImFuYXR0LWFwaSIsInRlbGVwaG9uZSI6Ijk1MDUxMTU1IiwiZW1haWwiOiJhZm9hZGV5QGdvdXYuYmoiLCJ1c2VyU3RhdHVzIjoiQUNUSVZBVEVEIiwiaW5pdFBhc3N3b3JkIjpmYWxzZSwiY2VudHJlSW1wb3QiOnsiaWQiOjk0LCJjb2RlIjoiSU1NQVQiLCJsaWJlbGxlIjoiU2VydmljZSBkJ2ltbWF0cmljdWxhdGlvbiBkZXMgcGVyc29ubmVzIHBoeXNpcXVlcyIsInR5cGVDZW50cmUiOiJBVVRSRSIsImRhdGVEZWJ1dCI6IjIwMjMtMDEtMTIgMTc6MzU6MDEiLCJkYXRlRmluIjpudWxsLCJvcGVyYXRvckRhdGUiOm51bGx9LCJyb2xlcyI6W3siaWQiOjExLCJuYW1lIjoiUk9MRV9VU0VSX0FQSSJ9XX0sImlhdCI6MTcwNjU0NzI3OSwiZXhwIjoxNzM4MTA0MjMxfQ.EH8MQpuzvFd3u1SjMJCUChx0zcbL--ensoTctIQ6mfvC2ouVwLCrMJ6AttMBuW9b0nuc_B0l4WAkiZZ8cFl4qA

# Session & Cache
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 🎨 Variables d'Environnement Frontend

### Portal (Nuxt.js) - .env.example

```env
VITE_API_URL="http://localhost:8001/api"
VITE_FEDAPAY_PUBLIC_KEY=""
VITE_CLIENT_ID=""
VITE_CLIENT_SECRET=""

VITE_PORTAL_URL="http://localhost:8003"
VITE_ADMIN_URL="http://localhost:3000"
VITE_AFFILIATE_URL="https://localhost:5173"
VITE_COOKIE_DOMAIN="localhost"
```

### Backoffice (Vue 3 + Vuero) - .env.example

```env
VITE_APP_NAME="Simveb"
VITE_API_URL=http://localhost:8002/api

# OAuth2 Credentials
VITE_CLIENT_ID="9d1ffaab-7aa4-43fe-9a90-2726fc316351"
VITE_CLIENT_SECRET="cwWM3KgXdMb91WnHrOzVGT1xNb3sQfRbRPjTKBHE"

VITE_PORTAL_URL="http://localhost:8003"
VITE_ADMIN_URL="http://localhost:3000"
VITE_COOKIE_DOMAIN="localhost"
```

### Affiliate (Vue 3) - .env.example

```env
VITE_APP_NAME="SIMVeB"
VITE_API_URL=http://localhost:8000/api
VITE_LOGIN_URL=http://localhost:8000/login

# OAuth2 Credentials
VITE_CLIENT_ID="9a03f3d1-8bcf-4d63-b649-980e327c0c86"
VITE_CLIENT_SECRET="JAPjLnGdS6gQK1WVUJ7SRLRbg0YKV9ZHbZVgh5s6"

# FedaPay Integration
VITE_FEDAPAY_PRIVATE_KEY="sk_sandbox_tclJppAyE-2EffzRl5VlZfgq"
VITE_FEDAPAY_PUBLIC_KEY="pk_sandbox_A86QDBH9O8mmnwf3qF0___ef"
VITE_FEDAPAY_API_URL="https://sandbox-api.fedapay.com"

VITE_PORTAL_URL="http://localhost:8003"
VITE_ADMIN_URL="http://localhost:3000"
VITE_COOKIE_DOMAIN="localhost"
```

---

## 👥 Comptes Utilisateurs Test

### 🔴 Mot de Passe Universel

**TOUS les comptes utilisateurs test partagent le même mot de passe:**

```
Mot de passe: here is the pass
```

### 1. 🔑 Super Admin (Tous Profils)

**Accès à TOUS les espaces et profils du système**

| Champ | Valeur |
|-------|--------|
| **Email** | nautilustest@mail.com |
| **NPI** | 8765432101 (env: local/dev/staging)<br>4811676017 (env: production) |
| **Téléphone** | +22951104856 (local/dev)<br>+22964000001 (prod) |
| **Prénom** | Admin |
| **Nom** | Admin |
| **Username** | 8765432101 |
| **Mot de passe** | here is the pass |

**Profils activés:**
- ✅ User (Citoyen)
- ✅ ANATT (Admin + Demand Manager)
- ✅ Police (Police Admin)
- ✅ Central Garage (CG Admin)
- ✅ GMA (Garage Municipal Atlantique - Admin)
- ✅ GMD (Garage Municipal Départemental - Admin)
- ✅ Auctioneer (Commissaire Priseur)
- ✅ Bank (Banque)
- ✅ Court (Greffier + Juge d'instruction)
- ✅ Distributor (Concessionnaire)
- ✅ Interpol
- ✅ Affiliate (Affilié - Admin)

**Fichier source:** `database/seeders/Staff/AnattAdminSeeder.php:30,69`

---

### 2. 👤 Utilisateur Simple (Citoyen)

| Champ | Valeur |
|-------|--------|
| **Email** | wwilliam@gmail.com |
| **NPI** | 2109876540 |
| **Téléphone** | +22992309876 |
| **Prénom** | William |
| **Nom** | WILSON |
| **Username** | 2109876540 |
| **Mot de passe** | here is the pass |
| **Profil** | User (Citoyen standard) |

**Fichier source:** `database/seeders/SimpleUserSeeder.php:27`

---

### 3. 🏢 Personnel ANATT

#### Staff ANATT #1 - Glory

| Champ | Valeur |
|-------|--------|
| **Email** | glory@example.com |
| **NPI** | 1632101074 |
| **Téléphone** | +2299270447 |
| **Prénom** | Glory |
| **Nom** | Gory |
| **Username** | 1632101074 |
| **Mot de passe** | here is the pass |
| **Rôle** | SERVICE_STAFF |

#### Staff ANATT #2 - Grace

| Champ | Valeur |
|-------|--------|
| **Email** | grace@example.com |
| **NPI** | 1065432101 |
| **Téléphone** | +22912387654 |
| **Prénom** | Grace |
| **Nom** | Grace |
| **Username** | 1065432101 |
| **Mot de passe** | here is the pass |
| **Rôle** | SERVICE_STAFF |

**Fichier source:** `database/seeders/Staff/AnattStaffSeeder.php:26-40`

---

### 4. 🚔 Police

| Champ | Valeur |
|-------|--------|
| **Email** | isabella@example.com |
| **NPI** | 9876543210 |
| **Téléphone** | +22961849888 |
| **Prénom** | Isabella |
| **Nom** | ANDERSON |
| **Username** | 9876543210 |
| **Mot de passe** | here is the pass |
| **Rôles** | Rôles du type Police |

**Fichier source:** `database/seeders/Staff/PoliceStaffSeeder.php:24`

---

### 5. 🌍 Interpol

| Champ | Valeur |
|-------|--------|
| **Email** | glory@example.com |
| **NPI** | 1632101074 |
| **Téléphone** | +22992704478 |
| **Prénom** | Glory |
| **Nom** | Gory |
| **Username** | 1632101074 |
| **Mot de passe** | here is the pass |
| **Rôle** | INTERPOL |

**Fichier source:** `database/seeders/Staff/InterpolStaffSeeder.php:26`

---

### 6. 🚗 Central Garage (CG)

| Champ | Valeur |
|-------|--------|
| **Email** | mia@example.com |
| **NPI** | 3610987650 |
| **Téléphone** | +22912389012 |
| **Prénom** | Mia |
| **Nom** | THOMPSON |
| **Username** | 3610987650 |
| **Mot de passe** | here is the pass |
| **Rôle** | CG_ADMIN |

**Fichier source:** `database/seeders/Staff/CGStaffSeeder.php:25`

---

### 7. 🏗️ GMA (Garage Municipal Atlantique)

| Champ | Valeur |
|-------|--------|
| **Email** | ava@example.com |
| **NPI** | 9987654320 |
| **Téléphone** | +22963000001 |
| **Prénom** | Ava |
| **Nom** | HALL |
| **Username** | 9987654320 |
| **Mot de passe** | here is the pass |
| **Rôle** | GMA_ADMIN |

**Fichier source:** `database/seeders/Staff/GMAStaffSeeder.php:25`

---

### 8. 🏗️ GMD (Garage Municipal Départemental)

| Champ | Valeur |
|-------|--------|
| **Email** | michael@example.com |
| **NPI** | 9826543210 |
| **Téléphone** | +22962000005 |
| **Prénom** | Michael |
| **Nom** | YOUNG |
| **Username** | 9826543210 |
| **Mot de passe** | here is the pass |
| **Rôle** | GMD_ADMIN |

**Fichier source:** `database/seeders/Staff/GMDStaffSeeder.php:26`

---

### 9. ⚖️ Commissaire Priseur (Auctioneer)

| Champ | Valeur |
|-------|--------|
| **Email** | benjamin@example.com |
| **NPI** | 4321998760 |
| **Téléphone** | +22991230987 |
| **Prénom** | Benjamin |
| **Nom** | MARTIN |
| **Username** | 4321998760 |
| **Mot de passe** | here is the pass |
| **Rôle** | AUCTIONEER |

**Fichier source:** `database/seeders/Staff/AuctioneerStaffSeeder.php:25`

---

### 10. 🏦 Banque

| Champ | Valeur |
|-------|--------|
| **Email** | bankeradmin@simveb.bj |
| **NPI** | 8823456789 |
| **Téléphone** | +22998894816 |
| **Prénom** | Peter |
| **Nom** | JOHNSON |
| **Username** | 8823456789 |
| **Mot de passe** | here is the pass |
| **Rôle** | BANK |

**Fichier source:** `database/seeders/Staff/BankSeeder.php:27`

---

### 11. 🚘 Concessionnaire (Distributor)

| Champ | Valeur |
|-------|--------|
| **Email** | contact@lesbagnoles.com |
| **NPI** | 1098765430 |
| **Téléphone** | +22982654091 |
| **Prénom** | Sophia |
| **Nom** | MOORE |
| **Username** | 1098765430 |
| **Mot de passe** | here is the pass |
| **Rôle** | DISTRIBUTOR |

**Fichier source:** `database/seeders/Staff/DistributorSeeder.php:28`

---

### 12. 🤝 Affilié

| Champ | Valeur |
|-------|--------|
| **Email** | alexander@example.com |
| **NPI** | 7109876540 |
| **Téléphone** | +22965000001 |
| **Prénom** | Alexander |
| **Nom** | LEWIS |
| **Username** | 7109876540 |
| **Mot de passe** | here is the pass |
| **Rôle** | AFFILIATE_MEMBER |

**Fichier source:** `database/seeders/Staff/AffiliateStaffSeeder.php:26`

---

## 💳 API Tiers - Credentials Sandbox

### 1. FedaPay (Paiement Mobile)

**Documentation:** https://docs.fedapay.com

#### Backend

```env
FEDAPAY_PUBLIC_KEY=pk_sandbox_cb7jY3KvZ_FLNFp_LfqenqZA
FEDAPAY_SECRET_KEY=sk_sandbox_WvbsARrZRcSxWd4ty8yn_3cj
FEDAPAY_ENVIRONMENT=sandbox
```

#### Frontend (Portal)

```env
VITE_FEDAPAY_PUBLIC_KEY=pk_sandbox_cb7jY3KvZ_FLNFp_LfqenqZA
```

#### Frontend (Affiliate)

```env
VITE_FEDAPAY_PRIVATE_KEY=sk_sandbox_tclJppAyE-2EffzRl5VlZfgq
VITE_FEDAPAY_PUBLIC_KEY=pk_sandbox_A86QDBH9O8mmnwf3qF0___ef
VITE_FEDAPAY_API_URL=https://sandbox-api.fedapay.com
```

**Services supportés:**
- ✅ MTN Mobile Money
- ✅ Moov Money
- ✅ Carte bancaire

**Fichier config:** `simveb-backend-develop/config/fedapay.php`

---

### 2. KKiaPay (Paiement Mobile)

**Documentation:** https://docs.kkiapay.me

```env
KKIAPAY_PUBLIC_KEY=5766c4e0824211efb2cd736c2a0bab43
KKIAPAY_SECRET_KEY=tpk_5766ebf1824211efb2cd736c2a0bab43
KKIAPAY_SECRET=tsk_5766ebf2824211efb2cd736c2a0bab43
KKIAPAY_SANDBOX=true
```

**Services supportés:**
- ✅ MTN Mobile Money
- ✅ Moov Money
- ✅ Visa/Mastercard

**Fichier config:** Intégré dans `.env`

---

### 3. Novu (Notifications Multi-Canal)

**Documentation:** https://docs.novu.co

```env
NOVU_SECRET_KEY=6dbc7c10270af82ccde1212b96a5d1c8
NOVU_PUBLIC_KEY=fBQFolGUuKeG
NOVU_ENV_ID=66843a0f8d5e505deeca014d
NOVU_BASE_API_URL=https://api.novu.co/v1/
```

**Canaux supportés:**
- ✅ Email
- ✅ SMS
- ✅ Push notifications
- ✅ In-app notifications

**Fichier config:** `simveb-backend-develop/config/novu.php`

---

### 4. Sentry (Monitoring d'Erreurs)

**Documentation:** https://docs.sentry.io

```env
SENTRY_LARAVEL_DSN=https://3c88e663578f3b27845c0cb554fdb51d@o4505782362177536.ingest.sentry.io/4505782364864512
SENTRY_TRACES_SAMPLE_RATE=1.0
```

**Fichier config:** `simveb-backend-develop/config/sentry.php`

---

### 5. APIs Gouvernementales (Sandbox)

#### ANIP (Agence Nationale d'Identification des Personnes)

**Endpoint de vérification NPI:**
```
POST https://sandbox-api.simveb-bj.com/api/persons
```

**Description:** Vérification de l'identité via le Numéro Personnel d'Identification (NPI)

#### DGI (Direction Générale des Impôts)

**Endpoint de vérification IFU:**
```
POST https://sandbox-api.simveb-bj.com/api/companies
```

**Token d'authentification:**
```
Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI1MDQiLCJVc2VyIjp7ImlkIjo1MDQsIm5vbSI6IkFOQVRUIEJFTklOIiwicHJlbm9tIjoiQU5BVFQgQkVOSU4iLCJ1c2VybmFtZSI6ImFuYXR0LWFwaSIsInRlbGVwaG9uZSI6Ijk1MDUxMTU1IiwiZW1haWwiOiJhZm9hZGV5QGdvdXYuYmoiLCJ1c2VyU3RhdHVzIjoiQUNUSVZBVEVEIiwiaW5pdFBhc3N3b3JkIjpmYWxzZSwiY2VudHJlSW1wb3QiOnsiaWQiOjk0LCJjb2RlIjoiSU1NQVQiLCJsaWJlbGxlIjoiU2VydmljZSBkJ2ltbWF0cmljdWxhdGlvbiBkZXMgcGVyc29ubmVzIHBoeXNpcXVlcyIsInR5cGVDZW50cmUiOiJBVVRSRSIsImRhdGVEZWJ1dCI6IjIwMjMtMDEtMTIgMTc6MzU6MDEiLCJkYXRlRmluIjpudWxsLCJvcGVyYXRvckRhdGUiOm51bGx9LCJyb2xlcyI6W3siaWQiOjExLCJuYW1lIjoiUk9MRV9VU0VSX0FQSSJ9XX0sImlhdCI6MTcwNjU0NzI3OSwiZXhwIjoxNzM4MTA0MjMxfQ.EH8MQpuzvFd3u1SjMJCUChx0zcbL--ensoTctIQ6mfvC2ouVwLCrMJ6AttMBuW9b0nuc_B0l4WAkiZZ8cFl4qA
```

**Description:** Vérification du statut fiscal via l'Identifiant Fiscal Unique (IFU)

#### Douane

**Base URL:**
```
https://sandbox-api.simveb-bj.com/api/
```

#### X-Road (Bus d'Intégration)

**Base URL:**
```
https://common-ss.xroad.bj:8443
```

**Description:** Bus d'intégration pour l'interopérabilité entre systèmes gouvernementaux

---

### 6. Vonage / Wirepick (SMS)

**Note:** Pas de credentials sandbox trouvés dans la configuration actuelle. Les services sont configurables via les variables d'environnement si nécessaire.

---

### 7. Mapbox (Cartographie)

**Note:** Pas de credentials trouvés dans la configuration actuelle. À configurer si nécessaire pour les fonctionnalités de géolocalisation.

---

## 💾 Base de Données

### PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=simveb
DB_USERNAME=simveb
DB_PASSWORD=password
```

**Accès via Docker:**
```bash
docker-compose exec db psql -U simveb -d simveb
```

**Commande de seed (avec tous les comptes test):**
```bash
docker-compose exec app php artisan db:seed
```

---

## 📧 Services d'Infrastructure

### SMTP (Gmail)

**Configuration actuelle:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=finplex.ntech@gmail.com
MAIL_PASSWORD=lofeslljqmuvdqal
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@simveb.bj"
MAIL_FROM_NAME="SimVeb"
```

**⚠️ AVERTISSEMENT:** Ces credentials SMTP sont dans le fichier .env.example et **NE DOIVENT PAS** être utilisés en production. Il s'agit probablement d'un compte test.

---

## 🌐 URLs des Services

### Backend API

```
http://localhost:8001/api
http://localhost:8002/api (alternative)
http://localhost:8000/api (affiliate)
```

### Frontend Applications

| Application | URL Locale | Port | Description |
|-------------|-----------|------|-------------|
| **Portal** | http://localhost:8003 | 8003 | Portail citoyen (Nuxt.js) |
| **Backoffice** | http://localhost:3000 | 3000 | Admin ANATT (Vue3/Vuero) |
| **Affiliate** | https://localhost:5173 | 5173 | Espace affilié (Vue3) |
| **Police** | https://localhost:5173 | 5173 | Espace police |
| **Interpol** | https://localhost:5173 | 5173 | Espace Interpol |
| **Bank** | https://localhost:5173 | 5173 | Espace banque |
| **GC** | https://localhost:5173 | 5173 | Central Garage |
| **Distributor** | https://localhost:5173 | 5173 | Concessionnaires |
| **Court** | https://localhost:5173 | 5173 | Justice |
| **GMA** | https://localhost:5173 | 5173 | Garage Municipal Atlantique |
| **GMD** | https://localhost:5173 | 5173 | Garage Municipal Départemental |

---

## 🔒 OAuth2 / Laravel Passport

### Client Credentials

#### Backoffice

```env
VITE_CLIENT_ID="9d1ffaab-7aa4-43fe-9a90-2726fc316351"
VITE_CLIENT_SECRET="cwWM3KgXdMb91WnHrOzVGT1xNb3sQfRbRPjTKBHE"
```

#### Affiliate

```env
VITE_CLIENT_ID="9a03f3d1-8bcf-4d63-b649-980e327c0c86"
VITE_CLIENT_SECRET="JAPjLnGdS6gQK1WVUJ7SRLRbg0YKV9ZHbZVgh5s6"
```

**Note:** Ces clients OAuth2 sont générés automatiquement par Laravel Passport lors de l'installation:

```bash
docker-compose exec app php artisan passport:install
```

---

## 🧪 Tests et Seed

### Initialiser la Base de Données avec les Données Test

```bash
# Migration + Seed
docker-compose exec app php artisan migrate:fresh --seed

# Seed uniquement
docker-compose exec app php artisan db:seed

# Seed d'un seeder spécifique
docker-compose exec app php artisan db:seed --class=SimpleUserSeeder
docker-compose exec app php artisan db:seed --class=AnattAdminSeeder
```

### Seeders Disponibles

| Seeder | Description | Fichier |
|--------|-------------|---------|
| **DatabaseSeeder** | Seed principal | `database/seeders/DatabaseSeeder.php` |
| **SimpleUserSeeder** | Citoyen simple | `database/seeders/SimpleUserSeeder.php` |
| **AnattAdminSeeder** | Super admin | `database/seeders/Staff/AnattAdminSeeder.php` |
| **PoliceStaffSeeder** | Personnel police | `database/seeders/Staff/PoliceStaffSeeder.php` |
| **InterpolStaffSeeder** | Personnel Interpol | `database/seeders/Staff/InterpolStaffSeeder.php` |
| **AnattStaffSeeder** | Personnel ANATT | `database/seeders/Staff/AnattStaffSeeder.php` |
| **CGStaffSeeder** | Central Garage | `database/seeders/Staff/CGStaffSeeder.php` |
| **GMAStaffSeeder** | GMA staff | `database/seeders/Staff/GMAStaffSeeder.php` |
| **GMDStaffSeeder** | GMD staff | `database/seeders/Staff/GMDStaffSeeder.php` |
| **BankSeeder** | Personnel banque | `database/seeders/Staff/BankSeeder.php` |
| **AuctioneerStaffSeeder** | Commissaires priseurs | `database/seeders/Staff/AuctioneerStaffSeeder.php` |
| **DistributorSeeder** | Concessionnaires | `database/seeders/Staff/DistributorSeeder.php` |
| **AffiliateStaffSeeder** | Affiliés | `database/seeders/Staff/AffiliateStaffSeeder.php` |

---

## 📝 Notes Importantes

### Sécurité

1. ⚠️ **Tous les mots de passe sont identiques** dans l'environnement de test: `here is the pass`
2. ⚠️ Les credentials dans `.env.example` sont des **exemples** et ne doivent **JAMAIS** être utilisés en production
3. ⚠️ Le token DGI est un token de test pour l'environnement sandbox
4. ⚠️ Les clés API FedaPay et KKiaPay sont en mode sandbox

### Environnements

Le système détecte automatiquement l'environnement via `app()->env`:
- **local** / **dev** / **development** / **staging**: Utilise les NPI et téléphones de test
- **production**: Utilise des identifiants différents (voir `AnattAdminSeeder.php:31`)

### Utilisation en Local

Pour tester le système complet:

1. Configurer le `.env` avec les variables de sandbox
2. Lancer Docker: `docker-compose up -d`
3. Migrer et seed: `docker-compose exec app php artisan migrate:fresh --seed`
4. Installer Passport: `docker-compose exec app php artisan passport:install`
5. Se connecter avec un compte test (ex: nautilustest@mail.com / here is the pass)

### Variables Manquantes

Certaines variables ne sont pas définies dans `.env.example`:
- `ANIP_BASE_URL` (vide)
- `DGI_BASE_URL` (vide)
- `DOUANE_BASE_URL` (vide)
- `MAPBOX_TOKEN` (absent)
- `VONAGE_API_KEY` (absent)

Ces services peuvent être configurés ultérieurement selon les besoins.

---

## 📚 Documentation Complémentaire

- **Guide de déploiement Windows**: `DEPLOYMENT_WINDOWS.md`
- **Documentation des APIs**: `API_DOCUMENTATION.md`
- **Rapport des workflows**: `WORKFLOWS_STATUS_REPORT.md`

---

## 🔄 Mise à Jour

**Dernière mise à jour:** 2025-11-25
**Version du document:** 1.0
**Mainteneur:** Claude AI Assistant

Pour toute question ou mise à jour des credentials, veuillez contacter l'équipe de développement SIMVEB.

---

**⚠️ RAPPEL DE SÉCURITÉ**

Ce document contient des credentials de test pour l'environnement sandbox uniquement.
**NE JAMAIS** utiliser ces credentials en production.
**NE JAMAIS** commiter ce fichier dans un repository public.
