# Documentation CI/CD SIMVEB

**Système d'Intégration et Déploiement Continu pour SIMVEB**

Version: 1.0
Date: 2025-12-08

---

## Table des Matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Architecture CI/CD](#2-architecture-cicd)
3. [Configuration par Composant](#3-configuration-par-composant)
4. [Variables d'Environnement](#4-variables-denvironnement)
5. [Pipelines et Stages](#5-pipelines-et-stages)
6. [Déploiement](#6-déploiement)
7. [Tests et Quality Gates](#7-tests-et-quality-gates)
8. [Docker Registry](#8-docker-registry)
9. [Schedules et Automatisation](#9-schedules-et-automatisation)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Vue d'ensemble

### 1.1 Objectifs

Le système CI/CD de SIMVEB a été conçu pour :

- ✅ **Automatiser** les builds, tests et déploiements
- ✅ **Garantir** la qualité du code via linting et tests
- ✅ **Sécuriser** avec des scans de sécurité automatiques
- ✅ **Déployer** de manière fiable sur dev, staging et production
- ✅ **Monitorer** les performances et erreurs
- ✅ **Notifier** l'équipe des changements

### 1.2 Technologies Utilisées

| Technologie | Usage |
|-------------|-------|
| **GitLab CI/CD** | Orchestration des pipelines |
| **Docker** | Conteneurisation des applications |
| **GitLab Container Registry** | Stockage des images Docker |
| **SSH** | Déploiement sur serveurs |
| **Sentry** | Monitoring d'erreurs |
| **Slack** | Notifications |

### 1.3 Architecture Globale

```
┌─────────────────────────────────────────────────────────────┐
│                  GitLab Repository                           │
│                  (Monorepo SIMVEB)                           │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  │ Git Push
                  ▼
┌─────────────────────────────────────────────────────────────┐
│           GitLab CI/CD Pipeline (Orchestrator)               │
│                   .gitlab-ci.yml                             │
└─────────────────┬───────────────────────────────────────────┘
                  │
    ┌─────────────┼─────────────┬─────────────┐
    │             │             │             │
    ▼             ▼             ▼             ▼
┌─────────┐ ┌─────────┐ ┌──────────┐ ┌──────────┐
│ Backend │ │ Portal  │ │Backoffice│ │Affiliate │
│Pipeline │ │Pipeline │ │ Pipeline │ │ Pipeline │
└────┬────┘ └────┬────┘ └─────┬────┘ └─────┬────┘
     │           │            │            │
     │ Build     │ Build      │ Build      │ Build
     │ Test      │ Test       │ Test       │ Test
     │ Docker    │ Docker     │ Docker     │ Docker
     │           │            │            │
     └───────────┴────────────┴────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│         GitLab Container Registry                            │
│  - simveb/backend:tag                                        │
│  - simveb/portal:tag                                         │
│  - simveb/backoffice:tag                                     │
│  - simveb/affiliate:tag                                      │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  │ Deploy
                  ▼
┌─────────────────────────────────────────────────────────────┐
│              Environments                                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                  │
│  │   DEV    │  │ STAGING  │  │   PROD   │                  │
│  └──────────┘  └──────────┘  └──────────┘                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Architecture CI/CD

### 2.1 Structure des Fichiers

```
projets01/
├── .gitlab-ci.yml                              # Pipeline orchestrateur principal
├── simveb-backend-develop/
│   └── .gitlab-ci.yml                          # Pipeline Backend (Laravel)
├── simveb-portal-design-develop/
│   └── .gitlab-ci.yml                          # Pipeline Portal (Nuxt 3)
├── simveb-backoffice-develop/
│   └── .gitlab-ci.yml                          # Pipeline Backoffice (Vue 3)
├── simveb-affiliate-develop/
│   └── .gitlab-ci.yml                          # Pipeline Affiliate (Vue 3)
└── CI_CD_DOCUMENTATION.md                      # Ce fichier
```

### 2.2 Stages Communs

Tous les pipelines suivent la même structure de stages :

```yaml
stages:
  - prepare      # Installation des dépendances
  - build        # Compilation du code
  - test         # Tests unitaires, E2E, linting
  - security     # Scans de sécurité
  - docker       # Build des images Docker
  - deploy       # Déploiement sur environnements
```

### 2.3 Flux de Travail

```
Commit → Push → Detect Changes → Trigger Builds → Tests → Security → Docker Build → Deploy
```

---

## 3. Configuration par Composant

### 3.1 Backend (Laravel)

**Fichier:** `simveb-backend-develop/.gitlab-ci.yml`

#### Stages

| Stage | Jobs | Description |
|-------|------|-------------|
| **prepare** | `composer:install` | Installation des dépendances PHP |
| **build** | `build:assets` | Cache Laravel (config, routes, views) |
| **test** | `phpunit:tests`<br>`pest:tests`<br>`lint:phpcs`<br>`lint:phpstan` | Tests unitaires et linting |
| **security** | `security:composer`<br>`security:sast` | Audits de sécurité |
| **docker** | `docker:build`<br>`docker:build:nginx` | Images Docker (PHP-FPM + Nginx) |
| **deploy** | `deploy:development`<br>`deploy:staging`<br>`deploy:production` | Déploiement par environnement |

#### Variables Requises

```yaml
# Database
DB_CONNECTION: pgsql
DB_HOST: postgres
DB_DATABASE: simveb_test
DB_USERNAME: simveb
DB_PASSWORD: secret

# SSH Deployment
SSH_PRIVATE_KEY: <SSH_KEY>
DEPLOY_HOST: <SERVER_IP>
DEPLOY_USER: <USER>
DEPLOY_HOST_STAGING: <STAGING_IP>
DEPLOY_HOST_PROD: <PROD_IP>

# Docker Registry
CI_REGISTRY: registry.gitlab.com
CI_REGISTRY_USER: <USER>
CI_REGISTRY_PASSWORD: <TOKEN>
```

#### Commandes Utiles

```bash
# Lancer les tests localement
composer install
php artisan test

# Lancer PHPUnit
vendor/bin/phpunit

# Lancer Pest
vendor/bin/pest

# Linter le code
vendor/bin/phpcs --standard=PSR12 app/
```

### 3.2 Portal (Nuxt 3)

**Fichier:** `simveb-portal-design-develop/.gitlab-ci.yml`

#### Stages

| Stage | Jobs | Description |
|-------|------|-------------|
| **prepare** | `pnpm:install` | Installation des dépendances Node |
| **build** | `build:nuxt`<br>`build:static` | Build Nuxt 3 (SSR + Static) |
| **test** | `test:unit`<br>`test:e2e`<br>`lint:eslint`<br>`typecheck` | Tests et validation |
| **security** | `security:audit` | Audit npm |
| **docker** | `docker:build` | Image Docker Nuxt |
| **deploy** | `deploy:development`<br>`deploy:staging`<br>`deploy:production` | Déploiement |

#### Variables Requises

```yaml
# Build Variables
VITE_API_URL: https://api.simveb-bj.com
VITE_CLIENT_ID: <CLIENT_ID>
VITE_CLIENT_SECRET: <CLIENT_SECRET>
VITE_FEDAPAY_PUBLIC_KEY: <FEDAPAY_KEY>
VITE_SENTRY_DSN: <SENTRY_DSN>

# Deployment
SSH_PRIVATE_KEY: <SSH_KEY>
DEPLOY_HOST: <SERVER_IP>
DEPLOY_USER: <USER>
```

#### Commandes Utiles

```bash
# Développement local
pnpm install
pnpm dev

# Build
pnpm build

# Tests
pnpm test

# Linting
pnpm lint
```

### 3.3 Backoffice (Vue 3)

**Fichier:** `simveb-backoffice-develop/.gitlab-ci.yml`

#### Stages

Similaire au Portal, avec spécificités Vite + Vue 3.

#### Variables Requises

```yaml
# Build Variables
VITE_API_URL: https://api.simveb-bj.com
VITE_CLIENT_ID: <CLIENT_ID>
VITE_CLIENT_SECRET: <CLIENT_SECRET>
VITE_MAPBOX_ACCESS_TOKEN: <MAPBOX_TOKEN>
VITE_SENTRY_DSN: <SENTRY_DSN>
GTM_ID: <GOOGLE_TAG_MANAGER_ID>

# Deployment
SSH_PRIVATE_KEY: <SSH_KEY>
DEPLOY_HOST: <SERVER_IP>
DEPLOY_USER: <USER>
```

### 3.4 Affiliate (Vue 3)

**Fichier:** `simveb-affiliate-develop/.gitlab-ci.yml`

Configuration similaire au Backoffice.

---

## 4. Variables d'Environnement

### 4.1 Configuration dans GitLab

**Navigation:** `Settings > CI/CD > Variables`

#### Variables Globales (Projet)

| Variable | Type | Protégé | Masqué | Description |
|----------|------|---------|--------|-------------|
| `CI_REGISTRY` | Variable | Non | Non | URL du registry Docker |
| `CI_REGISTRY_USER` | Variable | Non | Oui | Utilisateur registry |
| `CI_REGISTRY_PASSWORD` | Variable | Non | Oui | Token registry |
| `SSH_PRIVATE_KEY` | File | Oui | Oui | Clé SSH pour déploiement |
| `SLACK_WEBHOOK_URL` | Variable | Non | Oui | Webhook Slack notifications |

#### Variables par Environnement

**Development:**
```yaml
VITE_API_URL: http://dev-api.simveb-bj.com
DEPLOY_HOST: 10.0.0.10
DEPLOY_USER: deploy
```

**Staging:**
```yaml
VITE_API_URL: https://staging-api.simveb-bj.com
DEPLOY_HOST: 10.0.0.20
DEPLOY_USER: deploy
```

**Production:**
```yaml
VITE_API_URL: https://api.simveb-bj.com
DEPLOY_HOST: 10.0.0.30
DEPLOY_USER: deploy
```

### 4.2 Variables Sensibles

⚠️ **Important:** Ne jamais committer les secrets dans le code !

**Secrets à configurer:**
- `SSH_PRIVATE_KEY` - Clé privée SSH
- `FEDAPAY_PUBLIC_KEY` / `SECRET_KEY`
- `KKIAPAY_PRIVATE_KEY`
- `SENTRY_DSN`
- `DB_PASSWORD`
- `PASSPORT_CLIENT_SECRET`

---

## 5. Pipelines et Stages

### 5.1 Pipeline Orchestrateur

**Fichier:** `.gitlab-ci.yml` (racine)

#### Fonctionnalités

1. **Détection des changements**
   - Analyse les diffs Git
   - Détermine quels composants ont changé
   - Déclenche uniquement les pipelines nécessaires

2. **Triggers conditionnels**
   - Backend changé → Trigger backend pipeline
   - Portal changé → Trigger portal pipeline
   - etc.

3. **Tests d'intégration**
   - Tests API cross-composants
   - Tests E2E complets

4. **Déploiement coordonné**
   - Déploie tous les composants ensemble
   - Gère l'ordre de déploiement

#### Exemple de Workflow

```yaml
# Si modification dans simveb-backend-develop/
detect:backend → trigger:backend → backend pipeline complet

# Si modification dans plusieurs composants
detect:all → trigger:all → pipelines en parallèle
```

### 5.2 Règles de Déclenchement

```yaml
# Déclenche pour branches
- branches

# Déclenche pour merge requests
- merge_requests

# Déclenche pour tags
- tags

# Ne déclenche PAS pour commits DRAFT ou WIP
- if: $CI_COMMIT_TITLE =~ /^DRAFT/ → never
- if: $CI_COMMIT_TITLE =~ /^WIP/ → never
```

### 5.3 Jobs Manuels vs Automatiques

**Automatiques:**
- Build
- Tests
- Linting
- Security scans
- Docker build

**Manuels (require click):**
- Déploiement Development
- Déploiement Staging
- Déploiement Production
- Rollback
- Database backup

---

## 6. Déploiement

### 6.1 Environnements

| Environnement | Branche | URL | Déploiement |
|---------------|---------|-----|-------------|
| **Development** | `develop`, `feature/*` | dev.simveb-bj.com | Manuel |
| **Staging** | `staging`, `release/*` | staging.simveb-bj.com | Manuel |
| **Production** | `main`, `master`, tags | simveb-bj.com | Manuel |

### 6.2 Processus de Déploiement

#### Development

```bash
1. Push to develop branch
2. Pipeline runs automatically
3. Go to GitLab > CI/CD > Pipelines
4. Click "Play" on deploy:development job
5. Confirm deployment
```

#### Staging

```bash
1. Merge develop → staging
2. Pipeline runs
3. Click "Play" on deploy:staging
4. Test on staging environment
```

#### Production

```bash
1. Merge staging → main/master
2. Pipeline runs
3. Tests must pass
4. Security audit must pass
5. Click "Play" on deploy:production
6. Backup database automatically
7. Deploy with zero-downtime
8. Health check runs
9. Notification sent
```

### 6.3 Stratégie de Déploiement

**Backend (Laravel):**
```bash
1. Pull latest Docker images
2. Backup database
3. Stop old containers
4. Start new containers
5. Run migrations
6. Clear caches
7. Warm up caches
```

**Frontend (Vue/Nuxt):**
```bash
1. Pull latest Docker image
2. Stop old container
3. Start new container
4. Health check
```

### 6.4 Rollback

En cas de problème en production :

```bash
1. Go to GitLab > CI/CD > Pipelines
2. Click "Play" on rollback:production job
3. Confirms rollback
4. System reverts to previous version
```

**Automatique pour le backend:**
- Git reset to previous commit
- Restore database backup
- Restart containers

---

## 7. Tests et Quality Gates

### 7.1 Backend (Laravel)

#### Tests Unitaires (PHPUnit/Pest)

```yaml
phpunit:tests:
  script:
    - vendor/bin/phpunit --coverage-text
  coverage: '/^\s*Lines:\s*\d+.\d+\%/'
```

**Requis pour Production:**
- ✅ Tous les tests doivent passer
- ✅ Couverture de code > 70% (recommandé)

#### Linting (PHPCS)

```yaml
lint:phpcs:
  script:
    - vendor/bin/phpcs --standard=PSR12 app/
```

**Standards:**
- PSR-12 (PHP Standards Recommendations)

#### Analyse Statique (PHPStan)

```yaml
lint:phpstan:
  script:
    - vendor/bin/phpstan analyse app/ --level=5
```

### 7.2 Frontend (Vue/Nuxt)

#### Tests Unitaires

```yaml
test:unit:
  script:
    - pnpm test
```

#### Tests E2E

```yaml
test:e2e:
  script:
    - pnpm test:e2e
```

#### Linting (ESLint)

```yaml
lint:eslint:
  script:
    - pnpm lint
```

#### Type Checking (TypeScript)

```yaml
typecheck:
  script:
    - pnpm typecheck
```

### 7.3 Security Scans

#### Composer Audit (Backend)

```yaml
security:composer:
  script:
    - composer audit --format=json
```

#### NPM Audit (Frontend)

```yaml
security:audit:
  script:
    - pnpm audit --prod
```

#### SAST (Static Application Security Testing)

```yaml
security:sast:
  image: registry.gitlab.com/security-products/semgrep
  script:
    - semgrep --config=auto --json
```

---

## 8. Docker Registry

### 8.1 Images Docker

Toutes les images sont stockées dans GitLab Container Registry :

```
registry.gitlab.com/<namespace>/simveb/
├── backend:latest
├── backend:v1.2.3
├── backend:<commit-sha>
├── backend-nginx:latest
├── portal:latest
├── backoffice:latest
└── affiliate:latest
```

### 8.2 Tags

| Tag | Description | Quand |
|-----|-------------|-------|
| `latest` | Dernière version de la branche par défaut | Commit sur main/master |
| `v1.2.3` | Version sémantique | Tag Git |
| `<commit-sha>` | SHA du commit | Chaque build |
| `develop` | Branche develop | Commit sur develop |

### 8.3 Nettoyage

**Politique de rétention recommandée:**
- Garder les 10 derniers tags
- Garder les images < 30 jours
- Garder tous les tags de version (v*)

**Configuration:** `Settings > Packages & Registries > Cleanup policies`

---

## 9. Schedules et Automatisation

### 9.1 Pipelines Programmés

**Configuration:** `CI/CD > Schedules`

#### Nightly Build

```yaml
Schedule: 0 2 * * *  # Tous les jours à 2h du matin
Variables:
  SCHEDULED_JOB: nightly
```

**Actions:**
- Build complet de tous les composants
- Tests complets
- Génération de rapports

#### Weekly Security Scan

```yaml
Schedule: 0 3 * * 0  # Tous les dimanches à 3h
Variables:
  SCHEDULED_JOB: security
```

**Actions:**
- Scan de sécurité complet
- Audit des dépendances
- Rapport de vulnérabilités

#### Monthly Database Backup

```yaml
Schedule: 0 4 1 * *  # Le 1er de chaque mois à 4h
Variables:
  SCHEDULED_JOB: backup
```

### 9.2 Auto-DevOps

**Activer dans:** `Settings > CI/CD > Auto DevOps`

- ✅ Auto-deploy (désactivé par défaut pour SIMVEB)
- ✅ Auto-test
- ✅ Auto-build

---

## 10. Troubleshooting

### 10.1 Problèmes Courants

#### Build Failed: Dependencies

**Erreur:**
```
ERROR: Could not install packages
```

**Solution:**
```bash
# Backend
composer clear-cache
composer install --no-cache

# Frontend
pnpm store prune
pnpm install --force
```

#### Docker Build Failed

**Erreur:**
```
ERROR: Cannot connect to Docker daemon
```

**Solution:**
- Vérifier que le runner a accès à Docker
- Vérifier la configuration du service `docker:dind`

#### SSH Deployment Failed

**Erreur:**
```
Permission denied (publickey)
```

**Solution:**
1. Vérifier que `SSH_PRIVATE_KEY` est configuré
2. Vérifier les permissions sur le serveur
3. Ajouter la clé publique sur le serveur

```bash
# Sur le serveur
cat ~/.ssh/authorized_keys
# Doit contenir la clé publique correspondant à SSH_PRIVATE_KEY
```

#### Tests Failed

**Erreur:**
```
PHPUnit tests failed
```

**Solution:**
```bash
# Lancer localement pour déboguer
php artisan test --filter=TestName

# Vérifier les logs
php artisan log:show
```

### 10.2 Logs et Debugging

#### Consulter les Logs

**GitLab:**
```
CI/CD > Pipelines > [Pipeline] > [Job] > View logs
```

**Backend (Laravel):**
```bash
# Sur le serveur
docker-compose logs -f app
docker-compose exec app tail -f storage/logs/laravel.log
```

**Frontend:**
```bash
docker logs simveb-portal -f
```

#### Debug Mode

Activer le debug sur un job spécifique :

```yaml
job_name:
  variables:
    CI_DEBUG_TRACE: "true"
```

### 10.3 Performance

#### Cache Optimization

**Vérifier le cache:**
```
CI/CD > Pipelines > [Pipeline] > Cache
```

**Clear cache:**
```yaml
# Ajouter au job
cache:
  policy: pull-push  # ou push
```

#### Artifact Size

**Problème:** Artifacts trop gros

**Solution:**
```yaml
artifacts:
  paths:
    - vendor/  # Éviter si possible
  expire_in: 1 day  # Réduire la durée
```

---

## Annexes

### A. Commandes GitLab CLI

```bash
# Lister les pipelines
glab ci list

# Voir le statut
glab ci status

# Déclencher un pipeline
glab ci trigger

# Voir les variables
glab ci variable list
```

### B. Checklist de Déploiement

**Avant Production:**
- [ ] Tests passent (backend + frontend)
- [ ] Security audit OK
- [ ] Backup database effectué
- [ ] Variables d'environnement configurées
- [ ] Sentry configuré
- [ ] Monitoring activé
- [ ] Équipe notifiée
- [ ] Plan de rollback prêt

**Après Production:**
- [ ] Health checks OK
- [ ] Logs vérifiés
- [ ] Monitoring vérifié
- [ ] Tests smoke réalisés
- [ ] Équipe notifiée du succès

### C. Contacts et Support

**DevOps Team:**
- Email: devops@simveb-bj.com
- Slack: #simveb-devops
- GitLab: @devops-team

**Escalation:**
- Tech Lead: @tech-lead
- CTO: @cto

---

## Versions

| Version | Date | Auteur | Changements |
|---------|------|--------|-------------|
| 1.0 | 2025-12-08 | DevOps Team | Documentation initiale CI/CD |

---

**Document généré le:** 2025-12-08
**Projet:** SIMVEB - CI/CD Pipeline
**Plateforme:** GitLab CI/CD
