# Architecture Microservices SIMVEB

**Système d'Immatriculation des Véhicules - Architecture Simple et Pragmatique**

Version: 2.0
Date: 2026-01-03

---

## Table des Matières

1. [Vision et Principes](#vision-et-principes)
2. [Architecture Proposée](#architecture-proposée)
3. [Découpage des Microservices](#découpage-des-microservices)
4. [Communication entre Services](#communication-entre-services)
5. [API Gateway](#api-gateway)
6. [Base de Données](#base-de-données)
7. [Déploiement](#déploiement)
8. [Plan de Migration](#plan-de-migration)

---

## Vision et Principes

### Objectifs

✅ **Simplicité** - Architecture facile à comprendre et maintenir
✅ **Scalabilité** - Scaling indépendant par service
✅ **Résilience** - Isolation des pannes
✅ **Évolutivité** - Facilité d'ajout de nouvelles fonctionnalités
✅ **Migration Progressive** - Pas de big bang, migration graduelle

### Principes Directeurs

1. **Domain-Driven Design** - Services organisés par domaine métier
2. **Single Responsibility** - Un service = une responsabilité
3. **Autonomie** - Chaque service gère ses propres données
4. **Communication Asynchrone** - Événements pour les opérations non-critiques
5. **API-First** - APIs bien définies entre services

---

## Architecture Proposée

### Vue d'Ensemble

```
┌────────────────────────────────────────────────────────────────┐
│                    CLIENTS (Frontends)                          │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                │
│   │  Portal  │    │Backoffice│    │Affiliate │                │
│   │ (Nuxt 3) │    │ (Vue 3)  │    │ (Vue 3)  │                │
│   └────┬─────┘    └────┬─────┘    └────┬─────┘                │
│        │               │               │                        │
└────────┼───────────────┼───────────────┼────────────────────────┘
         │               │               │
         └───────────────┼───────────────┘
                         │
         ┌───────────────▼───────────────┐
         │       API GATEWAY              │
         │   (Kong / Traefik / Nginx)    │
         │  - Authentication              │
         │  - Rate Limiting               │
         │  - Load Balancing              │
         │  - Routing                     │
         └───────────────┬───────────────┘
                         │
         ┌───────────────┴────────────────────────────┐
         │                                             │
    ┌────▼────────┐  ┌────────────┐  ┌──────────────┐│
    │   Auth      │  │  Vehicle    │  │   Payment    ││
    │   Service   │  │  Service    │  │   Service    ││
    └────┬────────┘  └────┬───────┘  └──────┬───────┘│
         │                │                  │         │
    ┌────▼────────┐  ┌────▼───────┐  ┌──────▼───────┐│
    │   User      │  │   Immat.   │  │   Document   ││
    │   Service   │  │   Service   │  │   Service    ││
    └────┬────────┘  └────┬───────┘  └──────┬───────┘│
         │                │                  │         │
    ┌────▼────────┐  ┌────▼───────┐  ┌──────▼───────┐│
    │Notification │  │Integration │  │   Config     ││
    │   Service   │  │   Service   │  │   Service    ││
    └─────────────┘  └────────────┘  └──────────────┘│
                                                       │
         ┌─────────────────────────────────────────────┘
         │
    ┌────▼────────┐
    │   Message   │
    │   Broker    │  RabbitMQ / Redis
    │  (Events)   │
    └─────────────┘
```

### Architecture en Couches

```
┌─────────────────────────────────────────────────┐
│            API Gateway Layer                     │
│  - Authentication & Authorization                │
│  - Rate Limiting & Throttling                   │
│  - Request Routing                              │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│         Business Services Layer                  │
│  - Core Microservices                           │
│  - Domain Logic                                 │
│  - Business Rules                               │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│          Data Access Layer                       │
│  - Database per Service                         │
│  - Caching (Redis)                              │
│  - Search (Elasticsearch - optional)            │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│        Infrastructure Layer                      │
│  - Service Discovery                            │
│  - Load Balancing                               │
│  - Monitoring & Logging                         │
└─────────────────────────────────────────────────┘
```

---

## Découpage des Microservices

### 1. Auth Service (Service d'Authentification)

**Responsabilités:**
- Authentification (Login/Logout)
- Gestion des tokens JWT
- OAuth2 / Passport
- Vérification OTP
- Gestion des sessions
- 2FA (Two-Factor Authentication)

**API Endpoints:**
```
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/refresh
POST   /api/auth/verify-otp
POST   /api/auth/request-otp
GET    /api/auth/me
```

**Base de données:**
- `auth_db` (PostgreSQL)
  - tokens
  - sessions
  - login_attempts
  - otp_verifications

**Technologies:**
- Laravel 10 / PHP 8.2
- Laravel Passport
- Redis (cache sessions)

---

### 2. User Service (Service Utilisateurs)

**Responsabilités:**
- Gestion des utilisateurs (CRUD)
- Gestion des profils
- Gestion des rôles et permissions
- Historique utilisateur
- KYC (Know Your Customer)
- ANIP Integration (NPI verification)

**API Endpoints:**
```
GET    /api/users
GET    /api/users/{id}
POST   /api/users
PUT    /api/users/{id}
DELETE /api/users/{id}
GET    /api/users/{id}/profile
PUT    /api/users/{id}/profile
GET    /api/users/{id}/roles
POST   /api/users/{id}/verify-npi
```

**Base de données:**
- `user_db` (PostgreSQL)
  - users
  - profiles (person/company)
  - roles
  - permissions
  - identities
  - addresses

**Technologies:**
- Laravel 10 / PHP 8.2
- Integration ANIP (X-Road)

---

### 3. Vehicle Service (Service Véhicules)

**Responsabilités:**
- Gestion des véhicules (CRUD)
- Catalogue véhicules (marques, modèles)
- Caractéristiques techniques
- Historique véhicule
- Import/Export données véhicules

**API Endpoints:**
```
GET    /api/vehicles
GET    /api/vehicles/{id}
POST   /api/vehicles
PUT    /api/vehicles/{id}
DELETE /api/vehicles/{id}
GET    /api/vehicles/{id}/history
GET    /api/vehicles/brands
GET    /api/vehicles/models
GET    /api/vehicles/types
```

**Base de données:**
- `vehicle_db` (PostgreSQL)
  - vehicles
  - brands
  - models
  - types
  - characteristics
  - vehicle_history

**Technologies:**
- Laravel 10 / PHP 8.2
- Elasticsearch (recherche - optionnel)

---

### 4. Immatriculation Service

**Responsabilités:**
- Demandes d'immatriculation
- Workflow d'immatriculation
- Validation des demandes
- Attribution des plaques
- Renouvellement
- Changement de propriétaire
- Duplicata

**API Endpoints:**
```
POST   /api/immatriculations
GET    /api/immatriculations/{id}
GET    /api/immatriculations
PUT    /api/immatriculations/{id}/status
POST   /api/immatriculations/{id}/validate
POST   /api/immatriculations/{id}/reject
GET    /api/immatriculations/{id}/workflow
POST   /api/immatriculations/renewal
POST   /api/immatriculations/transfer
POST   /api/immatriculations/duplicate
```

**Base de données:**
- `immat_db` (PostgreSQL)
  - immatriculations
  - registration_requests
  - workflow_steps
  - plate_assignments
  - renewal_history
  - transfer_history

**Technologies:**
- Laravel 10 / PHP 8.2
- State Machine (workflow)

---

### 5. Payment Service (Service Paiements)

**Responsabilités:**
- Gestion des paiements
- Intégration FedaPay
- Intégration KKiaPay
- Facturation
- Historique des transactions
- Remboursements

**API Endpoints:**
```
POST   /api/payments/initiate
GET    /api/payments/{id}
GET    /api/payments/transactions
POST   /api/payments/{id}/confirm
POST   /api/payments/{id}/cancel
POST   /api/payments/{id}/refund
GET    /api/payments/invoice/{id}
```

**Base de données:**
- `payment_db` (PostgreSQL)
  - payments
  - transactions
  - invoices
  - refunds
  - payment_methods

**Technologies:**
- Laravel 10 / PHP 8.2
- FedaPay SDK
- KKiaPay SDK

---

### 6. Document Service (Service Documents)

**Responsabilités:**
- Gestion des documents
- Upload de fichiers
- Validation des documents
- Génération de PDFs
- Stockage sécurisé
- Archivage

**API Endpoints:**
```
POST   /api/documents/upload
GET    /api/documents/{id}
GET    /api/documents
DELETE /api/documents/{id}
GET    /api/documents/{id}/download
POST   /api/documents/generate-certificate
POST   /api/documents/generate-receipt
```

**Base de données:**
- `document_db` (PostgreSQL)
  - documents
  - document_types
  - document_validations
- File Storage (S3 / MinIO / Local)

**Technologies:**
- Laravel 10 / PHP 8.2
- Storage S3-compatible
- PDF Generation (DomPDF / Snappy)

---

### 7. Notification Service

**Responsabilités:**
- Envoi d'emails
- Envoi de SMS
- Push notifications
- Notifications in-app
- Templates de notifications
- Historique notifications

**API Endpoints:**
```
POST   /api/notifications/email
POST   /api/notifications/sms
POST   /api/notifications/push
GET    /api/notifications
GET    /api/notifications/templates
```

**Base de données:**
- `notification_db` (PostgreSQL)
  - notifications
  - notification_logs
  - templates

**Technologies:**
- Laravel 10 / PHP 8.2
- Queue (Redis / RabbitMQ)
- SMTP / SMS Provider

---

### 8. Integration Service (Service Intégrations)

**Responsabilités:**
- Intégration ANIP (X-Road)
- Intégration DGI (Impôts)
- Autres intégrations externes
- Orchestration des appels externes
- Gestion des timeouts et retries

**API Endpoints:**
```
POST   /api/integrations/anip/verify-npi
POST   /api/integrations/dgi/check-tax
GET    /api/integrations/status
```

**Base de données:**
- `integration_db` (PostgreSQL)
  - integration_logs
  - external_requests
  - retry_queue

**Technologies:**
- Laravel 10 / PHP 8.2
- X-Road Client
- HTTP Client with Circuit Breaker

---

### 9. Config Service (Service Configuration)

**Responsabilités:**
- Configuration centralisée
- Gestion des paramètres système
- Feature flags
- Gestion des services
- Tarification

**API Endpoints:**
```
GET    /api/config/settings
GET    /api/config/services
GET    /api/config/fees
GET    /api/config/features
```

**Base de données:**
- `config_db` (PostgreSQL)
  - settings
  - services
  - fees
  - feature_flags

**Technologies:**
- Laravel 10 / PHP 8.2
- Cache (Redis)

---

## Communication entre Services

### Communication Synchrone (HTTP REST)

Utilisée pour les opérations **critiques** nécessitant une réponse immédiate.

**Exemple:** Auth Service ↔ User Service

```php
// Auth Service appelle User Service
$response = Http::withToken($serviceToken)
    ->get('http://user-service/api/users/' . $userId);
```

**Outils:**
- HTTP REST avec JSON
- Service Discovery (Consul / Eureka - optionnel)
- Circuit Breaker (gestion des pannes)

### Communication Asynchrone (Events)

Utilisée pour les opérations **non-critiques** et **découplage**.

**Exemple:** Immatriculation créée → Notification envoyée

```php
// Immat Service publie un événement
event(new ImmatriculationCreated($immatriculation));

// Notification Service écoute l'événement
class SendImmatriculationNotification
{
    public function handle(ImmatriculationCreated $event)
    {
        // Envoyer notification
    }
}
```

**Outils:**
- RabbitMQ (recommandé)
- Redis Pub/Sub (alternative simple)
- Laravel Events + Queue

### Patterns de Communication

#### 1. Request/Response (Synchrone)

```
Client → API Gateway → Service A → Service B → Response
```

**Utilisation:** Opérations CRUD, lectures

#### 2. Event-Driven (Asynchrone)

```
Service A → Event Bus → Service B, C, D
```

**Utilisation:** Notifications, audit, analytics

#### 3. Saga Pattern (Transactions Distribuées)

```
Service A → Service B → Service C
   ↓ (rollback si échec)
Service A ← Service B ← Service C
```

**Utilisation:** Processus métier multi-services (ex: immatriculation complète)

---

## API Gateway

### Rôle de l'API Gateway

```
┌─────────────────────────────────────────┐
│           API GATEWAY                    │
├─────────────────────────────────────────┤
│  1. Authentification & Authorization    │
│  2. Rate Limiting                       │
│  3. Request Routing                     │
│  4. Load Balancing                      │
│  5. SSL Termination                     │
│  6. CORS Handling                       │
│  7. Request/Response Transformation     │
│  8. Logging & Monitoring                │
└─────────────────────────────────────────┘
```

### Options d'Implémentation

#### Option 1: Kong (Recommandé)

**Avantages:**
- ✅ Open source mature
- ✅ Plugins riches (auth, rate limit, etc.)
- ✅ Dashboard UI
- ✅ Haute performance
- ✅ Écosystème large

**Configuration:**
```yaml
# kong.yml
services:
  - name: auth-service
    url: http://auth-service:8001
    routes:
      - name: auth-route
        paths:
          - /api/auth
    plugins:
      - name: rate-limiting
        config:
          minute: 100

  - name: vehicle-service
    url: http://vehicle-service:8002
    routes:
      - name: vehicle-route
        paths:
          - /api/vehicles
    plugins:
      - name: jwt
```

#### Option 2: Traefik (Simple)

**Avantages:**
- ✅ Configuration automatique (Docker labels)
- ✅ Reverse proxy moderne
- ✅ SSL automatique (Let's Encrypt)
- ✅ Dashboard inclus

**Configuration:**
```yaml
# docker-compose.yml
services:
  traefik:
    image: traefik:v2.10
    command:
      - --api.dashboard=true
      - --providers.docker=true
    labels:
      - "traefik.enable=true"

  auth-service:
    labels:
      - "traefik.http.routers.auth.rule=PathPrefix(`/api/auth`)"
      - "traefik.http.services.auth.loadbalancer.server.port=8001"
```

#### Option 3: Nginx (Basique)

**Avantages:**
- ✅ Très simple
- ✅ Performant
- ✅ Bien connu

**Configuration:**
```nginx
# nginx.conf
upstream auth_service {
    server auth-service:8001;
}

upstream vehicle_service {
    server vehicle-service:8002;
}

server {
    listen 80;

    location /api/auth {
        proxy_pass http://auth_service;
    }

    location /api/vehicles {
        proxy_pass http://vehicle_service;
    }
}
```

---

## Base de Données

### Stratégie: Database per Service

Chaque microservice possède sa **propre base de données**.

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ Auth Service │    │ User Service │    │Vehicle Svc   │
└──────┬───────┘    └──────┬───────┘    └──────┬───────┘
       │                   │                   │
   ┌───▼────┐          ┌───▼────┐         ┌───▼────┐
   │auth_db │          │user_db │         │vehicle │
   │        │          │        │         │  _db   │
   └────────┘          └────────┘         └────────┘
```

### Avantages

✅ **Isolation** - Panne d'une DB n'affecte pas les autres
✅ **Scaling** - Scaling indépendant par service
✅ **Technologie** - Possibilité d'utiliser différentes DB
✅ **Autonomie** - Équipes indépendantes

### Gestion des Données Partagées

#### Pattern 1: API Calls

```php
// Vehicle Service a besoin des infos utilisateur
$user = Http::get('http://user-service/api/users/' . $userId)->json();
```

#### Pattern 2: Event Sourcing + CQRS

```php
// User Service publie un événement
UserUpdated::dispatch($user);

// Vehicle Service maintient une copie en cache
class SyncUserCache {
    public function handle(UserUpdated $event) {
        Cache::put('user:' . $event->user->id, $event->user);
    }
}
```

#### Pattern 3: Shared Database (à éviter si possible)

Utiliser uniquement pour les données vraiment partagées (configuration, référentiels).

---

## Déploiement

### Docker Compose - Architecture Microservices

```yaml
version: '3.8'

services:
  # API Gateway
  api-gateway:
    image: kong:latest
    ports:
      - "8000:8000"
      - "8001:8001"
    environment:
      KONG_DATABASE: postgres
      KONG_PG_HOST: postgres
    networks:
      - simveb-network

  # Auth Service
  auth-service:
    build: ./services/auth-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: auth_db
      REDIS_HOST: redis
    networks:
      - simveb-network

  # User Service
  user-service:
    build: ./services/user-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: user_db
    networks:
      - simveb-network

  # Vehicle Service
  vehicle-service:
    build: ./services/vehicle-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: vehicle_db
    networks:
      - simveb-network

  # Immatriculation Service
  immat-service:
    build: ./services/immat-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: immat_db
    networks:
      - simveb-network

  # Payment Service
  payment-service:
    build: ./services/payment-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: payment_db
    networks:
      - simveb-network

  # Document Service
  document-service:
    build: ./services/document-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: document_db
    networks:
      - simveb-network

  # Notification Service
  notification-service:
    build: ./services/notification-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: notification_db
      REDIS_HOST: redis
    networks:
      - simveb-network

  # Integration Service
  integration-service:
    build: ./services/integration-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: integration_db
    networks:
      - simveb-network

  # Config Service
  config-service:
    build: ./services/config-service
    environment:
      DB_HOST: postgres
      DB_DATABASE: config_db
      REDIS_HOST: redis
    networks:
      - simveb-network

  # PostgreSQL
  postgres:
    image: postgres:15-alpine
    environment:
      POSTGRES_USER: simveb
      POSTGRES_PASSWORD: simveb_password
    volumes:
      - postgres-data:/var/lib/postgresql/data
    networks:
      - simveb-network

  # Redis
  redis:
    image: redis:7-alpine
    networks:
      - simveb-network

  # RabbitMQ (Message Broker)
  rabbitmq:
    image: rabbitmq:3-management-alpine
    ports:
      - "5672:5672"
      - "15672:15672"
    networks:
      - simveb-network

networks:
  simveb-network:
    driver: bridge

volumes:
  postgres-data:
```

---

## Plan de Migration

### Approche: Strangler Fig Pattern

Migration **progressive** du monolithe vers microservices.

```
Phase 1: Monolithe Existant
┌─────────────────────────┐
│   Backend Monolithe     │
│     (Laravel)           │
│   Toutes les features   │
└─────────────────────────┘

Phase 2: Extraction Premier Service
┌─────────────────────────┐    ┌──────────────┐
│   Backend Monolithe     │    │Auth Service  │
│   Moins Auth            │◄───┤ (extrait)    │
└─────────────────────────┘    └──────────────┘

Phase 3: Extraction Progressive
┌─────────────────────────┐
│   Backend Monolithe     │    ┌──────────────┐
│   (de - en -)           │    │Auth Service  │
└─────────────────────────┘    ├──────────────┤
                               │User Service  │
                               ├──────────────┤
                               │Payment Svc   │
                               └──────────────┘

Phase 4: Microservices Complets
                               ┌──────────────┐
                               │Auth Service  │
                               ├──────────────┤
                               │User Service  │
                               ├──────────────┤
                               │Vehicle Svc   │
                               ├──────────────┤
                               │Immat Service │
                               ├──────────────┤
                               │Payment Svc   │
                               ├──────────────┤
                               │Document Svc  │
                               ├──────────────┤
                               │Notif Service │
                               ├──────────────┤
                               │Integration   │
                               ├──────────────┤
                               │Config Svc    │
                               └──────────────┘
```

### Roadmap de Migration

#### Phase 1: Préparation (2 semaines)

**Actions:**
- [ ] Analyser le monolithe et identifier les bounded contexts
- [ ] Choisir l'API Gateway (Kong recommandé)
- [ ] Mettre en place l'infrastructure de base
- [ ] Former l'équipe aux microservices

**Livrables:**
- Documentation d'architecture
- Infrastructure de base (API Gateway + Message Broker)

#### Phase 2: Premier Microservice - Auth Service (3 semaines)

**Actions:**
- [ ] Extraire le code d'authentification
- [ ] Créer la base de données auth_db
- [ ] Migrer les données
- [ ] Configurer l'API Gateway
- [ ] Tests d'intégration
- [ ] Déploiement en staging

**Livrables:**
- Auth Service fonctionnel
- Tests passants
- Documentation API

#### Phase 3: Services Critiques (6 semaines)

**Services à extraire:**
1. User Service (2 semaines)
2. Payment Service (2 semaines)
3. Notification Service (2 semaines)

**Actions par service:**
- Extraction code
- Migration données
- Tests
- Déploiement

#### Phase 4: Services Métier (8 semaines)

**Services à extraire:**
1. Vehicle Service (3 semaines)
2. Immatriculation Service (3 semaines)
3. Document Service (2 semaines)

#### Phase 5: Services Supports (4 semaines)

**Services à extraire:**
1. Integration Service (2 semaines)
2. Config Service (2 semaines)

#### Phase 6: Décommissionnement Monolithe (2 semaines)

**Actions:**
- [ ] Vérifier que tous les services sont migrés
- [ ] Rediriger tout le trafic vers les microservices
- [ ] Désactiver le monolithe
- [ ] Nettoyage

**Total: ~25 semaines (~6 mois)**

---

## Avantages et Challenges

### Avantages de l'Architecture Microservices

✅ **Scalabilité** - Scaling indépendant par service
✅ **Résilience** - Isolation des pannes
✅ **Déploiement** - Déploiements indépendants
✅ **Technologie** - Stack technique par service
✅ **Équipes** - Équipes autonomes par service
✅ **Maintenance** - Code plus petit et focalisé

### Challenges

⚠️ **Complexité** - Plus de services à gérer
⚠️ **Données** - Pas de transactions ACID entre services
⚠️ **Réseau** - Latence réseau entre services
⚠️ **Monitoring** - Monitoring distribué plus complexe
⚠️ **Testing** - Tests d'intégration plus complexes
⚠️ **Déploiement** - Orchestration nécessaire

---

## Conclusion

Cette architecture microservices simple et pragmatique pour SIMVEB permet:

1. **Migration progressive** - Pas de big bang
2. **Isolation** - Services indépendants
3. **Scalabilité** - Scaling par service
4. **Maintenabilité** - Code focalisé
5. **Évolutivité** - Ajout facile de services

**Recommandations:**
- Commencer par **Auth Service** (moins de dépendances)
- Utiliser **Kong** comme API Gateway
- Implémenter **Circuit Breaker** dès le début
- Monitoring avec **Prometheus + Grafana** (déjà en place)
- Logs centralisés avec **Loki** (déjà en place)

---

**Version:** 2.0
**Date:** 2026-01-03
**Auteur:** DevOps Team
**Projet:** SIMVEB - Architecture Microservices
