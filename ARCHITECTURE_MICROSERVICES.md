# Architecture Microservices SIMVEB

**Système d'Immatriculation des Véhicules du Bénin**

Version: 1.0
Date: 2025-12-05

---

## Table des Matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Architecture Backend](#2-architecture-backend)
3. [Architecture Frontend](#3-architecture-frontend)
4. [APIs et Endpoints](#4-apis-et-endpoints)
5. [Modèles de Données](#5-modèles-de-données)
6. [Services Métier](#6-services-métier)
7. [Microservices Internes](#7-microservices-internes)
8. [Intégrations Externes](#8-intégrations-externes)
9. [Infrastructure et Déploiement](#9-infrastructure-et-déploiement)
10. [Sécurité](#10-sécurité)
11. [Flux de Données](#11-flux-de-données)
12. [Monitoring et Observabilité](#12-monitoring-et-observabilité)

---

## 1. Vue d'ensemble

### 1.1 Architecture Globale

SIMVEB est un système monolithique modulaire composé de **4 composants principaux** :

```
┌─────────────────────────────────────────────────────────────┐
│                    SIMVEB ECOSYSTEM                          │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐   │
│  │    Portal     │  │  Backoffice   │  │   Affiliate   │   │
│  │   (Nuxt 3)    │  │    (Vue 3)    │  │    (Vue 3)    │   │
│  │   Public      │  │     Admin     │  │  Institutions │   │
│  └───────┬───────┘  └───────┬───────┘  └───────┬───────┘   │
│          │                  │                  │             │
│          └──────────────────┼──────────────────┘             │
│                             │                                │
│                    ┌────────▼────────┐                       │
│                    │  Backend API    │                       │
│                    │   (Laravel)     │                       │
│                    │   400+ Routes   │                       │
│                    └────────┬────────┘                       │
│                             │                                │
│         ┌───────────────────┼───────────────────┐           │
│         │                   │                   │           │
│    ┌────▼────┐      ┌──────▼──────┐    ┌──────▼──────┐    │
│    │PostgreSQL│     │   ANIP      │    │    DGI      │    │
│    │   DB     │     │  (X-Road)   │    │  (Impôts)   │    │
│    └──────────┘     └─────────────┘    └─────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Composants Principaux

| Composant | Type | Technologie | Rôle |
|-----------|------|-------------|------|
| **simveb-backend-develop** | API Backend | Laravel 10 + PHP 8.2 | API REST, logique métier |
| **simveb-portal-design-develop** | Portal Public | Nuxt 3 + Vue 3 | Interface citoyens |
| **simveb-backoffice-develop** | Backoffice Admin | Vue 3 + Vite | Interface administration |
| **simveb-affiliate-develop** | Interface Affiliés | Vue 3 + Vite | Accès institutions |

### 1.3 Technologies Clés

**Backend:**
- Laravel 10.x
- PHP 8.2.0 - 8.3.x
- PostgreSQL
- Laravel Passport (OAuth2)
- Docker + Docker Compose

**Frontend:**
- Nuxt 3.10.1 (Portal)
- Vue 3 (Backoffice & Affiliate)
- Pinia (State Management)
- Tailwind CSS
- Axios

---

## 2. Architecture Backend

### 2.1 Structure Backend

```
simveb-backend-develop/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 128 contrôleurs
│   │   │   ├── Admin/          # Gestion administration
│   │   │   ├── Auth/           # Authentification
│   │   │   ├── Client/         # API clients
│   │   │   ├── Portal/         # API portal public
│   │   │   ├── Vehicle/        # Gestion véhicules
│   │   │   ├── Space/          # Espaces de travail
│   │   │   ├── Immatriculation/
│   │   │   └── ...
│   │   ├── Middleware/         # 12 middlewares
│   │   ├── Requests/           # Validation
│   │   └── Resources/          # Transformations API
│   │
│   ├── Models/                 # 124 modèles
│   │   ├── Account/            # User, Customer, Declarant
│   │   ├── Auth/               # Profile, Role, Permission
│   │   ├── Config/             # Configurations
│   │   ├── Immatriculation/    # Immatriculations
│   │   ├── Order/              # Orders, Demands, Invoices
│   │   ├── Vehicle/            # Véhicules
│   │   ├── Space/              # Espaces
│   │   └── ...
│   │
│   ├── Services/               # 58 services métier
│   │   ├── Declaration/
│   │   ├── Demand/
│   │   ├── Duplicate/
│   │   ├── External/           # Intégrations externes
│   │   ├── Immatriculation/
│   │   └── Treatment/
│   │
│   ├── Repositories/           # Repository Pattern
│   └── Traits/                 # Code réutilisable
│
├── ntech-libs/                 # Packages internes
│   ├── users-package/
│   ├── metadata-package/
│   ├── activity-log-package/
│   ├── notifier-package/
│   └── required-document-package/
│
├── routes/
│   ├── api.php
│   ├── portal-routes.php
│   ├── clients-routes.php
│   ├── immatriculation-routes.php
│   ├── vehicle-routes.php
│   ├── treatments-routes.php
│   └── spaces.php
│
└── database/
    └── migrations/
```

### 2.2 Patterns Architecturaux

#### Repository Pattern
```php
// Abstraction de l'accès aux données
Repositories/
├── VehicleRepository.php
├── ImmatriculationRepository.php
├── OrderRepository.php
└── ...
```

#### Service Layer
```php
// Logique métier isolée
Services/
├── Immatriculation/
│   ├── ImmatriculationService.php
│   └── ReimmatriculationService.php
├── Demand/
│   └── DemandService.php
└── Treatment/
    └── TreatmentService.php
```

#### Request Validation
```php
// Form Requests pour validation centralisée
Http/Requests/
├── Vehicle/CreateVehicleRequest.php
├── Order/SubmitOrderRequest.php
└── ...
```

### 2.3 Dépendances Principales

```json
{
  "laravel/framework": "^10.10",
  "laravel/passport": "^11.3",
  "spatie/laravel-permission": "^5.7",
  "fedapay/fedapay-php": "^0.4.0",
  "barryvdh/laravel-dompdf": "^2.1",
  "maatwebsite/excel": "^3.1",
  "sentry/sentry-laravel": "^3.8",
  "laravel-notification-channels/novu": "^1.3",
  "pestphp/pest": "^2.35"
}
```

---

## 3. Architecture Frontend

### 3.1 Portal Public (simveb-portal-design-develop)

**Rôle:** Interface publique pour les citoyens

**Technologies:**
- Nuxt 3.10.1
- Vue 3.4.15
- Tailwind CSS
- Pinia (State Management)
- Sentry (Monitoring)

**Structure:**
```
simveb-portal-design-develop/
├── pages/
│   ├── auth/
│   │   ├── login.vue
│   │   └── register.vue
│   ├── services/
│   │   ├── immatriculation/
│   │   ├── reimmatriculation/
│   │   ├── mutation/
│   │   ├── duplicate/
│   │   └── transformation/
│   ├── my-cars/
│   │   ├── index.vue
│   │   └── [id].vue
│   └── file-status/
│       └── index.vue
│
├── stores/                     # Pinia stores
│   ├── auth.js
│   ├── vehicle.js
│   ├── order.js
│   └── ...
│
├── components/
│   ├── forms/
│   ├── cards/
│   └── modals/
│
└── middleware/
    ├── auth.js
    └── guest.js
```

**Fonctionnalités principales:**
- Authentification citoyens (OTP)
- Demande d'immatriculation en ligne
- Suivi de demandes
- Consultation de véhicules
- Services de transformation
- Paiements en ligne (FedaPay, KkiaPay)
- Téléchargement de documents

### 3.2 Backoffice Admin (simveb-backoffice-develop)

**Rôle:** Interface d'administration du système

**Technologies:**
- Vue 3.4.27
- Vite 5.0.0
- Bulma CSS + Tailwind CSS
- Pinia 2.1.6
- ApexCharts 3.42.0

**Structure:**
```
simveb-backoffice-develop/
├── src/
│   ├── pages/
│   │   ├── demands/            # Gestion demandes
│   │   ├── orders/             # Gestion commandes
│   │   ├── config/             # Configuration système
│   │   ├── vehicles/           # Gestion véhicules
│   │   ├── Opposition/         # Oppositions
│   │   ├── PledgesIssueRequest/ # Nantissements
│   │   ├── users/              # Gestion utilisateurs
│   │   ├── statistics/         # Statistiques
│   │   └── ...
│   │
│   ├── stores/
│   │   └── modules/
│   │       ├── auth/
│   │       ├── demand/
│   │       ├── order/
│   │       ├── vehicle/
│   │       └── ...
│   │
│   ├── components/
│   ├── utils/
│   │   └── api/
│   └── routes/
│       └── index.js
```

**Fonctionnalités principales:**
- Gestion des demandes et commandes
- Traitement des dossiers
- Configuration système (types, tarifs, zones)
- Statistiques et rapports
- Gestion des utilisateurs/rôles
- Gestion des affiliés
- Suivi oppositions/nantissements
- Listes noires (véhicules/personnes)
- Export de données (Excel, PDF)

### 3.3 Interface Affiliés (simveb-affiliate-develop)

**Rôle:** Accès pour institutions partenaires

**Technologies:**
- Vue 3.2.45
- Vite 5.0.8
- Tailwind CSS
- Pinia 2.1.7
- Tabulator Tables

**Structure:**
```
simveb-affiliate-develop/
├── src/
│   ├── pages/
│   │   ├── affiliate/          # Général affiliés
│   │   ├── police/             # Interface Police
│   │   ├── interpol/           # Interface Interpol
│   │   ├── bank/               # Interface Banques
│   │   ├── garage/             # Garages certifiés
│   │   ├── distributor/        # Distributeurs
│   │   ├── auctioneer/         # Commissaires-priseurs
│   │   ├── court/              # Justice
│   │   └── gma-gmd/            # GMA/GMD
│   │
│   ├── views/
│   ├── stores/
│   └── router/
```

**Profils d'accès:**
- Police
- Interpol
- Banques
- Garages certifiés
- Distributeurs
- Commissaires-priseurs
- Justice (Cours)
- GMA/GMD (Gestionnaires)

**Fonctionnalités:**
- Consultation véhicules
- Alertes véhicules
- Recherche d'immatriculations
- Statistiques
- Historique des consultations

---

## 4. APIs et Endpoints

### 4.1 Vue d'ensemble

Le backend expose **plus de 400 endpoints** REST organisés par domaine fonctionnel.

### 4.2 Authentification & Session

```
POST   /api/login/send-otp          # Envoyer OTP
POST   /api/login                   # Se connecter
POST   /api/logout                  # Se déconnecter
GET    /api/current-user            # Utilisateur actuel
POST   /api/register/*              # Inscription
POST   /api/refresh-token           # Rafraîchir token
```

### 4.3 Véhicules

```
GET    /api/vehicles                # Liste véhicules
POST   /api/vehicles                # Créer véhicule
GET    /api/vehicles/{id}           # Détails véhicule
PUT    /api/vehicles/{id}           # Modifier véhicule
DELETE /api/vehicles/{id}           # Supprimer véhicule

GET    /api/vehicles/{id}/plates    # Plaques d'un véhicule
GET    /api/vehicle-types           # Types de véhicules
GET    /api/vehicle-categories      # Catégories
GET    /api/vehicle-brands          # Marques
POST   /api/vehicle-passages        # Passages frontières
```

### 4.4 Immatriculation

```
POST   /api/immatriculation-demands        # Créer demande
GET    /api/immatriculation/{id}           # Détails
GET    /api/immatriculation-types          # Types
GET    /api/immatriculation-formats        # Formats
POST   /api/reimmatriculation-demands      # Ré-immatriculation
POST   /api/mutation-demands               # Mutation
```

### 4.5 Demandes & Commandes

```
GET    /api/demands                 # Liste demandes
POST   /api/demands                 # Créer demande
GET    /api/demands/{id}            # Détails demande
PUT    /api/demands/{id}            # Modifier demande

GET    /api/orders                  # Liste commandes
POST   /api/orders                  # Créer commande
POST   /api/submit-order            # Soumettre commande
GET    /api/orders/{id}             # Détails commande

GET    /api/invoices                # Liste factures
GET    /api/invoices/{id}           # Détails facture
```

### 4.6 Portal (API Publique)

```
GET    /api/portal/services                        # Services disponibles
GET    /api/portal/immatriculation-search          # Recherche
POST   /api/portal/transactions                    # Créer transaction
POST   /api/portal/vehicle-administrative-status   # Statut véhicule
GET    /api/portal/my-vehicles                     # Mes véhicules
```

### 4.7 Client

```
GET    /api/client/services                # Services clients
POST   /api/client/add-demand-to-cart      # Ajouter au panier
POST   /api/client/validate-cart           # Valider panier
GET    /api/client/get-vehicles            # Mes véhicules
GET    /api/client/get-cart                # Mon panier
```

### 4.8 Administration

```
GET    /api/admin-demands               # Demandes admin
GET    /api/admin-orders                # Commandes admin
GET    /api/stats/*                     # Statistiques multiples
POST   /api/exports/*                   # Exports (Excel, PDF)
GET    /api/admin/dashboard             # Tableau de bord
```

### 4.9 Espaces (Spaces)

```
GET    /api/spaces                          # Liste espaces
POST   /api/spaces                          # Créer espace
GET    /api/spaces/{id}                     # Détails espace
POST   /api/space-registration-requests     # Demande inscription
POST   /api/space-suspension-requests       # Suspension
```

### 4.10 Configurations

```
GET    /api/zones                   # Zones géographiques
GET    /api/tariffs                 # Tarifs
GET    /api/plate-colors            # Couleurs de plaques
GET    /api/plate-shapes            # Formes de plaques
GET    /api/service-types           # Types de services
```

### 4.11 Oppositions & Nantissements

```
POST   /api/oppositions                    # Créer opposition
GET    /api/oppositions/{id}               # Détails opposition
POST   /api/opposition-lifts               # Levée d'opposition

POST   /api/pledges                        # Créer nantissement
GET    /api/pledges/{id}                   # Détails nantissement
POST   /api/pledge-lifts                   # Levée de nantissement
```

---

## 5. Modèles de Données

### 5.1 Domaine Véhicules

```php
Vehicle                    # Véhicule principal
VehicleType               # Type (voiture, moto, camion...)
VehicleCategory           # Catégorie
VehicleBrand              # Marque
VehicleOwner              # Propriétaire
VehicleCharacteristic     # Caractéristiques techniques
VehiclePassage            # Passages frontières
VehicleAlert              # Alertes véhicule
GmaVehicle, GmdVehicle    # Véhicules spéciaux gouvernementaux
```

### 5.2 Domaine Immatriculation

```php
Immatriculation           # Immatriculation
ImmatriculationType       # Type d'immatriculation
ImmatriculationFormat     # Format (AA-0000-BB, etc.)
ImmatriculationTreatment  # Traitement
Reimmatriculation         # Ré-immatriculation
Mutation                  # Mutation (changement propriétaire)
```

### 5.3 Domaine Documents

```php
GrayCard                  # Carte grise
GrayCardDuplicate         # Duplicata carte grise
Plate                     # Plaque d'immatriculation
PlateDuplicate            # Duplicata plaque
PlateColor                # Couleur de plaque
PlateShape                # Forme de plaque
Certificate               # Certificat
```

### 5.4 Domaine Commandes

```php
Order                     # Commande
Demand                    # Demande
Invoice                   # Facture
Transaction               # Transaction paiement
PrintOrder                # Ordre d'impression
PlateOrder                # Commande de plaques
```

### 5.5 Domaine Utilisateurs

```php
Profile                   # Profil utilisateur
ProfileType               # Type de profil
Role                      # Rôle (admin, agent, client...)
Permission                # Permission
Invitation                # Invitation
Space                     # Espace de travail
```

### 5.6 Domaine Spécial

```php
Pledge                    # Nantissement
PledgeLift                # Levée de nantissement
Opposition                # Opposition
BlacklistVehicle          # Véhicules blacklistés
BlacklistPerson           # Personnes blacklistées
```

### 5.7 Relations Clés

```
Vehicle (1) ─── (N) Plate
Vehicle (1) ─── (1) GrayCard
Vehicle (1) ─── (N) VehicleOwner
Vehicle (1) ─── (N) Immatriculation
Vehicle (1) ─── (N) VehiclePassage

Order (1) ─── (N) Demand
Order (1) ─── (1) Invoice
Order (1) ─── (N) Transaction

Immatriculation (1) ─── (1) Vehicle
Immatriculation (N) ─── (1) ImmatriculationType

Pledge (1) ─── (1) Vehicle
Opposition (1) ─── (1) Vehicle
```

---

## 6. Services Métier

### 6.1 Services d'Immatriculation

#### Immatriculation Initiale
- Enregistrement nouveau véhicule
- Attribution numéro d'immatriculation
- Génération carte grise
- Production plaques
- Validation documents

#### Ré-immatriculation
- Changement de format
- Mise à jour informations
- Conservation historique
- Nouvelle plaque si nécessaire

#### Mutation
- Changement de propriétaire
- Transfert de titre
- Validation documents
- Mise à jour carte grise

#### Duplicata
- Carte grise perdue/volée
- Plaques endommagées
- Vérification identité
- Génération nouveaux documents

#### Transformation Véhicule
- Modification caractéristiques
- Changement d'usage
- Validation technique
- Mise à jour carte grise

### 6.2 Services Administratifs

#### Gestion des Demandes
```php
Services/Demand/
├── DemandService.php           # Logique principale
├── DemandValidationService.php # Validation
└── DemandTreatmentService.php  # Traitement
```

#### Gestion des Commandes
```php
Services/Order/
├── OrderService.php            # Gestion commandes
├── CartService.php             # Panier
└── InvoiceService.php          # Facturation
```

#### Traitement des Paiements
```php
Services/Payment/
├── FedaPayService.php          # Intégration FedaPay
├── KkiaPayService.php          # Intégration KkiaPay
└── PaymentVerificationService.php
```

### 6.3 Services de Configuration

```php
Services/Config/
├── TariffService.php           # Gestion tarifs
├── ZoneService.php             # Zones géographiques
├── VehicleTypeService.php      # Types véhicules
└── PlateFormatService.php      # Formats plaques
```

### 6.4 Services Externes

```php
Services/External/
├── AnipService.php             # ANIP (identité)
├── DgiService.php              # DGI (impôts)
├── CustomsService.php          # Douanes
└── XRoadService.php            # X-Road gateway
```

---

## 7. Microservices Internes

SIMVEB utilise une architecture **modular monolith** avec des packages internes préparant la migration vers de vrais microservices.

### 7.1 users-package (ntech/users-package)

**Responsabilité:** Gestion des utilisateurs et authentification

```php
ntech-libs/users-package/
├── src/
│   ├── Models/
│   │   ├── Profile.php
│   │   ├── Role.php
│   │   └── Permission.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   └── UserService.php
│   └── Providers/
│       └── UsersServiceProvider.php
```

**Fonctionnalités:**
- Authentification (Passport)
- Gestion des profils
- Rôles et permissions (Spatie)
- Multi-espaces (Spaces)

### 7.2 metadata-package (ntech/metadata-package)

**Responsabilité:** Gestion des métadonnées système

```php
ntech-libs/metadata-package/
├── src/
│   ├── Models/
│   │   └── Metadata.php
│   ├── Services/
│   │   └── MetadataService.php
│   └── Providers/
│       └── MetadataServiceProvider.php
```

**Fonctionnalités:**
- Stockage de métadonnées
- Configuration dynamique
- Cache de métadonnées

### 7.3 activity-log-package (ntech/activity-log-package)

**Responsabilité:** Traçabilité et audit

```php
ntech-libs/activity-log-package/
├── src/
│   ├── Models/
│   │   └── ActivityLog.php
│   ├── Services/
│   │   └── ActivityLogService.php
│   ├── Traits/
│   │   └── LogsActivity.php
│   └── Providers/
│       └── ActivityLogServiceProvider.php
```

**Fonctionnalités:**
- Enregistrement automatique des actions
- Audit trail complet
- Historique des modifications
- Recherche dans les logs

### 7.4 notifier-package (ntech/notifier-package)

**Responsabilité:** Système de notifications multi-canaux

```php
ntech-libs/notifier-package/
├── src/
│   ├── Services/
│   │   ├── EmailNotifier.php
│   │   ├── SmsNotifier.php
│   │   └── PushNotifier.php
│   ├── Channels/
│   │   └── NovuChannel.php
│   └── Providers/
│       └── NotifierServiceProvider.php
```

**Fonctionnalités:**
- Email (SMTP)
- SMS (Vonage)
- Push notifications
- Intégration Novu
- Templates de notifications

### 7.5 required-document-package (ntech/required-document-package)

**Responsabilité:** Gestion des documents requis

```php
ntech-libs/required-document-package/
├── src/
│   ├── Models/
│   │   └── RequiredDocument.php
│   ├── Services/
│   │   ├── DocumentService.php
│   │   └── ValidationService.php
│   └── Providers/
│       └── RequiredDocumentServiceProvider.php
```

**Fonctionnalités:**
- Définition documents requis par service
- Validation de documents
- Gestion des uploads
- Vérification de complétude

---

## 8. Intégrations Externes

### 8.1 ANIP (Agence Nationale d'Identification des Personnes)

**Protocole:** SOAP/XML via X-Road
**Port:** 8443
**URL:** `https://common-ss.xroad.bj:8443`

**Fonctionnalités:**
- Vérification NPI (Numéro Personnel d'Identification)
- Récupération identité citoyens
- Validation biométrique

**Exemple d'appel:**
```php
Services/External/AnipService.php

public function checkNpi(string $npi): array
{
    // Appel SOAP via X-Road
    $client = new SoapClient($this->xroadUrl);
    return $client->verifyIdentity(['npi' => $npi]);
}
```

### 8.2 DGI (Direction Générale des Impôts)

**Protocole:** REST API
**URL:** `https://sandbox-api.simveb-bj.com/api/companies`

**Fonctionnalités:**
- Vérification IFU (Identifiant Fiscal Unique)
- Récupération info entreprises
- Validation statut fiscal

**Authentification:** Token JWT

### 8.3 Douanes

**Protocole:** REST API
**URL:** `https://sandbox-api.simveb-bj.com/api/`

**Fonctionnalités:**
- Vérification déclaration douanière
- Validation véhicules importés
- Récupération documents douaniers

### 8.4 Passerelles de Paiement

#### FedaPay
```php
"fedapay/fedapay-php": "^0.4.0"

Configuration:
FEDAPAY_PUBLIC_KEY=pk_sandbox_***
FEDAPAY_SECRET_KEY=sk_sandbox_***
FEDAPAY_ENVIRONMENT=sandbox
```

**Services supportés:**
- Cartes bancaires
- Mobile Money (MTN, Moov, etc.)

#### KkiaPay
```php
Configuration:
KKIAPAY_PUBLIC_KEY=***
KKIAPAY_PRIVATE_KEY=***
KKIAPAY_SECRET=***
KKIAPAY_SANDBOX=true
```

**Services supportés:**
- Mobile Money
- Cartes bancaires

### 8.5 Monitoring Externe

#### Sentry
```
SENTRY_LARAVEL_DSN=https://***@sentry.io/***
SENTRY_TRACES_SAMPLE_RATE=1.0
```

**Fonctionnalités:**
- Error tracking
- Performance monitoring
- Release tracking

#### Novu
```
NOVU_SECRET_KEY=***
```

**Fonctionnalités:**
- Notifications multi-canaux
- Templates centralisés
- Historique notifications

---

## 9. Infrastructure et Déploiement

### 9.1 Architecture Docker

```yaml
# docker-compose.yml
services:
  webserver:
    image: nginx:1.21.6-alpine
    ports:
      - "8004:80"
    volumes:
      - ./:/var/www
    networks:
      - app-network

  app:
    build:
      context: ./
      dockerfile: Dockerfile
    volumes:
      - ./:/var/www
    environment:
      - PHP_VERSION=8.2
    networks:
      - app-network

  db:
    image: postgres:latest
    container_name: simveb_db_server
    environment:
      POSTGRES_DB: simveb
      POSTGRES_USER: simveb
      POSTGRES_PASSWORD: secret
    ports:
      - "5432:5432"
    volumes:
      - db-data:/var/lib/postgresql/data
    networks:
      - app-network

  test_db:
    image: postgres:latest
    container_name: simveb_test_db_server
    environment:
      POSTGRES_DB: simveb_test
    networks:
      - app-network

networks:
  app-network:
    driver: bridge

volumes:
  db-data:
```

### 9.2 Dockerfile Backend

```dockerfile
FROM php:8.2-fpm

# Extensions PHP requises
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    bcmath \
    sockets

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Application
WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
```

### 9.3 Configuration Nginx

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 9.4 Ports et URLs

| Service | Port | URL Development |
|---------|------|-----------------|
| Backend API | 8004 | http://localhost:8004 |
| Portal Public | 8003 | http://localhost:8003 |
| Backoffice | 3000 | http://localhost:3000 |
| Affiliate | 5173 | https://localhost:5173 |
| PostgreSQL | 5432 | localhost:5432 |

### 9.5 Variables d'Environnement

#### Backend (.env)
```bash
# Application
APP_NAME=SIMVEB
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.simveb-bj.com

# Database
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=simveb
DB_USERNAME=simveb
DB_PASSWORD=***

# OAuth2
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=***
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=***

# X-Road
XROAD_BASE_URL=https://common-ss.xroad.bj:8443
XROAD_CLIENT_ID=***
XROAD_SERVICE_CODE=***

# APIs Externes
CHECK_NPI_URL=https://sandbox-api.simveb-bj.com/api/persons
CHECK_IFU_URL=https://sandbox-api.simveb-bj.com/api/companies
DOUANE_API=https://sandbox-api.simveb-bj.com/api/

# Paiements
FEDAPAY_PUBLIC_KEY=pk_sandbox_***
FEDAPAY_SECRET_KEY=sk_sandbox_***
KKIAPAY_PUBLIC_KEY=***
KKIAPAY_PRIVATE_KEY=***
KKIAPAY_SANDBOX=true

# Notifications
NOVU_SECRET_KEY=***
MAIL_MAILER=smtp
VONAGE_KEY=***

# Monitoring
SENTRY_LARAVEL_DSN=***
TELESCOPE_ENABLED=false
```

#### Frontend (.env)
```bash
# Portal
VITE_API_URL=http://localhost:8002/api
VITE_CLIENT_ID=***
VITE_CLIENT_SECRET=***
VITE_FEDAPAY_PUBLIC_KEY=pk_sandbox_***
VITE_PORTAL_URL=http://localhost:8003
VITE_ADMIN_URL=http://localhost:3000
VITE_SENTRY_DSN=***
```

---

## 10. Sécurité

### 10.1 Authentification

#### OAuth2 avec Laravel Passport

```php
// Token personnel d'accès
POST /api/login
{
    "email": "user@example.com",
    "otp": "123456"
}

Response:
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 7200
}

// Utilisation
GET /api/current-user
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

**Configuration:**
- Durée de session: 120 minutes
- Refresh tokens: Activés
- Grant types: Password, Client Credentials, Personal Access

### 10.2 Middlewares de Sécurité

```php
app/Http/Middleware/

1. Authenticate.php
   - Vérifie token OAuth2
   - Redirige si non authentifié

2. SpaceAccessMiddleware.php
   - Contrôle accès aux espaces
   - Vérifie appartenance à l'espace

3. PermissionMiddleware.php
   - Vérifie permissions utilisateur
   - Basé sur Spatie Laravel Permission

4. VerifyCsrfToken.php
   - Protection CSRF
   - Exceptions pour API

5. TrustProxies.php
   - Gestion proxies
   - Headers sécurisés

6. ForceJsonResponse.php
   - Force réponses JSON API
   - Gestion erreurs
```

### 10.3 Rôles et Permissions

```php
// Configuration Spatie Permission
Rôles principaux:
- super-admin
- admin
- agent
- client
- affiliate

Permissions par domaine:
- vehicles.view
- vehicles.create
- vehicles.update
- vehicles.delete

- demands.view
- demands.create
- demands.approve
- demands.reject

- orders.view
- orders.process
- orders.complete

- config.manage
- users.manage
- stats.view
```

### 10.4 Multi-Espaces (Multi-Tenancy)

```php
// Un utilisateur peut appartenir à plusieurs espaces
User
├── Space 1 (Agence Cotonou)
├── Space 2 (Agence Porto-Novo)
└── Space 3 (Service Central)

// Middleware vérifie accès à l'espace
Route::middleware(['auth', 'space.access'])->group(function () {
    // Routes protégées
});

// Header requis
X-Current-Space: {space_id}
```

### 10.5 Validation et Sanitization

```php
// Form Requests avec règles de validation
class CreateVehicleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'chassis_number' => 'required|unique:vehicles|max:17',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'owner_npi' => 'required|size:10',
            // ...
        ];
    }
}

// Sanitization automatique
- TrimStrings middleware
- XSS protection (htmlspecialchars)
- SQL injection (PDO prepared statements)
```

### 10.6 Protection CSRF

```php
// Routes API exemptées
protected $except = [
    'api/*',
];

// Formulaires web
<form>
    @csrf
    <!-- ... -->
</form>
```

### 10.7 Rate Limiting

```php
// config/passport.php
'throttle' => [
    'max_attempts' => 5,
    'decay_minutes' => 1,
],

// Routes API
Route::middleware(['throttle:60,1'])->group(function () {
    // Max 60 requêtes par minute
});
```

---

## 11. Flux de Données

### 11.1 Flux d'Immatriculation Initiale

```
┌─────────────┐
│   Citoyen   │
│   (Portal)  │
└──────┬──────┘
       │ 1. Demande d'immatriculation
       ▼
┌─────────────────────────────────┐
│  Backend API                     │
│  POST /api/immatriculation-      │
│       demands                    │
└──────┬──────────────────────────┘
       │ 2. Validation documents
       ▼
┌─────────────┐    ┌─────────────┐
│    ANIP     │    │     DGI     │
│ (vérif NPI) │    │ (vérif IFU) │
└──────┬──────┘    └──────┬──────┘
       │                  │
       │ 3. Identité OK   │ Fiscal OK
       ▼                  ▼
┌─────────────────────────────────┐
│  Création Order + Demand         │
│  Calcul tarifs                   │
└──────┬──────────────────────────┘
       │ 4. Génération facture
       ▼
┌─────────────┐
│  Invoice    │
│  created    │
└──────┬──────┘
       │ 5. Paiement
       ▼
┌─────────────────────────────────┐
│  FedaPay / KkiaPay              │
│  Transaction                     │
└──────┬──────────────────────────┘
       │ 6. Paiement confirmé
       ▼
┌─────────────────────────────────┐
│  Backoffice                      │
│  Agent traite la demande         │
└──────┬──────────────────────────┘
       │ 7. Validation finale
       ▼
┌─────────────────────────────────┐
│  Génération documents:           │
│  - Carte grise (PDF)             │
│  - Numéro d'immatriculation      │
│  - Ordre impression plaques      │
└──────┬──────────────────────────┘
       │ 8. Notification citoyen
       ▼
┌─────────────┐
│    Novu     │
│ Email + SMS │
└─────────────┘
```

### 11.2 Flux de Mutation (Changement Propriétaire)

```
┌──────────────────┐
│ Nouveau          │
│ Propriétaire     │
└────────┬─────────┘
         │ 1. Demande mutation
         ▼
┌──────────────────────────────┐
│ POST /api/mutation-demands   │
│ - NPI nouveau propriétaire   │
│ - Documents transfert        │
└────────┬─────────────────────┘
         │ 2. Vérifications
         ▼
┌─────────────┐    ┌──────────────┐
│    ANIP     │    │   Vehicle    │
│(nouveau NPI)│    │ (ancien prop)│
└──────┬──────┘    └──────┬───────┘
       │                  │
       │ OK               │ Vérifié
       ▼                  ▼
┌──────────────────────────────┐
│  Création demande mutation   │
│  Statut: En attente paiement │
└────────┬─────────────────────┘
         │ 3. Paiement
         ▼
┌──────────────────────────────┐
│  Paiement confirmé           │
└────────┬─────────────────────┘
         │ 4. Traitement backoffice
         ▼
┌──────────────────────────────┐
│  Mise à jour:                │
│  - VehicleOwner (nouveau)    │
│  - GrayCard (nouveau nom)    │
│  - Historique conservé       │
└────────┬─────────────────────┘
         │ 5. Notification
         ▼
┌──────────────────────────────┐
│  Nouveau propriétaire notifié│
│  Ancien propriétaire notifié │
└──────────────────────────────┘
```

### 11.3 Flux Nantissement (Pledge)

```
┌─────────────┐
│   Banque    │
│ (Affiliate) │
└──────┬──────┘
       │ 1. Demande nantissement
       ▼
┌─────────────────────────────┐
│ POST /api/pledges           │
│ - vehicle_id                │
│ - bank_id                   │
│ - amount                    │
│ - duration                  │
└──────┬──────────────────────┘
       │ 2. Création demande
       ▼
┌─────────────────────────────┐
│ Pledge créé                 │
│ Statut: Pending             │
│ Assigné à: Greffier         │
└──────┬──────────────────────┘
       │ 3. Notification greffier
       ▼
┌─────────────┐
│  Greffier   │
│ (Backoffice)│
└──────┬──────┘
       │ 4. Validation/Rejet
       ▼
┌─────────────────────────────┐
│ Si Validé:                  │
│ - Vehicle.pledged = true    │
│ - Pledge.status = Approved  │
│ - Notification banque       │
│                             │
│ Si Rejeté:                  │
│ - Pledge.status = Rejected  │
│ - Reason enregistrée        │
└──────┬──────────────────────┘
       │ 5. Résultat
       ▼
┌─────────────┐
│   Banque    │
│  notifiée   │
└─────────────┘
```

### 11.4 Flux Opposition

```
┌─────────────┐
│   Police    │
│ (Affiliate) │
└──────┬──────┘
       │ 1. Signalement vol
       ▼
┌─────────────────────────────┐
│ POST /api/oppositions       │
│ - vehicle_id                │
│ - reason: "Vol"             │
│ - police_report_number      │
└──────┬──────────────────────┘
       │ 2. Création opposition
       ▼
┌─────────────────────────────┐
│ Opposition créée            │
│ Vehicle.opposition = true   │
│ Véhicule bloqué             │
└──────┬──────────────────────┘
       │ 3. Notifications
       ▼
┌───────────────┬─────────────┬──────────────┐
│  Propriétaire │   Interpol  │  Frontières  │
│   notifié     │   alerté    │   alertées   │
└───────────────┴─────────────┴──────────────┘
       │
       │ 4. Véhicule retrouvé
       ▼
┌─────────────────────────────┐
│ POST /api/opposition-lifts  │
│ - opposition_id             │
│ - verification_proof        │
└──────┬──────────────────────┘
       │ 5. Levée opposition
       ▼
┌─────────────────────────────┐
│ Vehicle.opposition = false  │
│ Véhicule débloqué           │
│ Propriétaire notifié        │
└─────────────────────────────┘
```

---

## 12. Monitoring et Observabilité

### 12.1 Sentry (Error Tracking)

**Configuration:**
```php
// Backend
'dsn' => env('SENTRY_LARAVEL_DSN'),
'traces_sample_rate' => 1.0,
'profiles_sample_rate' => 1.0,

// Frontend (Portal)
Sentry.init({
  dsn: "...",
  integrations: [
    new Sentry.BrowserTracing(),
    new Sentry.Replay(),
  ],
  tracesSampleRate: 1.0,
  replaysSessionSampleRate: 0.1,
});
```

**Fonctionnalités:**
- Error tracking automatique
- Performance monitoring
- Release tracking
- Breadcrumbs
- User context

### 12.2 Laravel Telescope

**Activation (dev uniquement):**
```php
TELESCOPE_ENABLED=true
```

**Inspection:**
- Requêtes HTTP
- Queries SQL
- Jobs/Queues
- Events
- Logs
- Exceptions
- Cache
- Mail

**Accès:**
```
http://localhost:8004/telescope
```

### 12.3 Activity Logs

**Package custom:** `ntech/activity-log-package`

```php
// Utilisation
use Ntech\ActivityLog\Traits\LogsActivity;

class Vehicle extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];
}

// Logs automatiques
Vehicle::create([...]);  // Log: "created"
$vehicle->update([...]);  // Log: "updated"
$vehicle->delete();       // Log: "deleted"

// Consultation
ActivityLog::forSubject($vehicle)->get();
```

**Informations enregistrées:**
- Action (created, updated, deleted, etc.)
- Utilisateur (user_id)
- Timestamp
- Ancien état (old)
- Nouvel état (new)
- IP address
- User agent

### 12.4 Métriques et Statistiques

**Endpoints statistiques:**
```
GET /api/stats/dashboard          # Tableau de bord général
GET /api/stats/demands             # Stats demandes
GET /api/stats/orders              # Stats commandes
GET /api/stats/vehicles            # Stats véhicules
GET /api/stats/transactions        # Stats financières
GET /api/stats/zones               # Stats par zone
```

**Métriques clés:**
- Nombre d'immatriculations (par période)
- Taux de traitement des demandes
- Revenus générés
- Temps moyen de traitement
- Répartition géographique
- Types de véhicules

### 12.5 Health Checks

```php
// Endpoint de santé
GET /api/health

Response:
{
    "status": "ok",
    "timestamp": "2025-12-05T10:30:00Z",
    "services": {
        "database": "ok",
        "redis": "ok",
        "storage": "ok",
        "anip": "ok",
        "dgi": "ok"
    }
}
```

### 12.6 Logging

**Configuration:**
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'sentry'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    'sentry' => [
        'driver' => 'sentry',
        'level' => 'error',
    ],
];
```

**Niveaux de logs:**
- DEBUG: Informations de débogage
- INFO: Événements généraux
- WARNING: Avertissements
- ERROR: Erreurs
- CRITICAL: Erreurs critiques

---

## Annexes

### A. Glossaire

| Terme | Description |
|-------|-------------|
| **NPI** | Numéro Personnel d'Identification (ANIP) |
| **IFU** | Identifiant Fiscal Unique (DGI) |
| **GMA** | Gestionnaire de Matériel Automobile |
| **GMD** | Gestionnaire de Matériel Diplomatique |
| **X-Road** | Plateforme d'échange de données gouvernementales |
| **Pledge** | Nantissement (mise en gage d'un véhicule) |
| **Opposition** | Blocage administratif d'un véhicule |
| **Space** | Espace de travail (agence, service) |

### B. Références

**Documentation API complète:**
- `/home/user/projets01/API_DOCUMENTATION.md`

**Credentials Sandbox:**
- `/home/user/projets01/SANDBOX_CREDENTIALS.md`

**Guide de Déploiement:**
- `/home/user/projets01/DEPLOYMENT_WINDOWS.md`
- `/home/user/projets01/README-DEPLOYMENT.md`

**Workflows:**
- `/home/user/projets01/WORKFLOWS_STATUS_REPORT.md`

### C. Versions

| Composant | Version |
|-----------|---------|
| Laravel | 10.10 |
| PHP | 8.2.0 - 8.3.x |
| PostgreSQL | Latest |
| Nuxt | 3.10.1 |
| Vue | 3.4.27 |
| Node.js | 18+ |

---

**Document généré le:** 2025-12-05
**Version:** 1.0
**Projet:** SIMVEB - Système d'Immatriculation des Véhicules du Bénin
