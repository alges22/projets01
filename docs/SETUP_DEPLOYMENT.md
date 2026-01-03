# Guide de Configuration Déploiement SIMVEB

## Table des Matières

1. [Architecture](#architecture)
2. [Configuration des VMs](#configuration-des-vms)
3. [Configuration GitLab CI/CD](#configuration-gitlab-cicd)
4. [Configuration des Branches](#configuration-des-branches)
5. [Déploiement](#déploiement)
6. [Monitoring et Maintenance](#monitoring-et-maintenance)
7. [Troubleshooting](#troubleshooting)

---

## Architecture

### Vue d'ensemble

```
┌─────────────────────────────────────────────────────┐
│              GitLab Repository                       │
│         https://gitlab.com/your-org/simveb          │
└───────────────────┬─────────────────────────────────┘
                    │
        ┌───────────┴───────────┐
        │                       │
   ┌────▼─────┐           ┌────▼─────┐
   │ staging  │           │   main   │
   │  branch  │           │  branch  │
   └────┬─────┘           └────┬─────┘
        │                      │
        │ Auto Deploy          │ Manual Deploy
        │                      │
┌───────▼────────┐      ┌──────▼────────┐
│  VM APP        │      │  VM APP       │
│  (STAGING)     │      │  (PRODUCTION) │
│                │      │               │
│ 10.x.x.10      │      │ 10.x.x.30     │
│                │      │               │
│ - Backend      │      │ - Backend     │
│ - Portal       │      │ - Portal      │
│ - Backoffice   │      │ - Backoffice  │
│ - Affiliate    │      │ - Affiliate   │
│ - Redis        │      │ - Redis       │
└───────┬────────┘      └──────┬────────┘
        │                      │
        │ PostgreSQL           │ PostgreSQL
        │                      │
┌───────▼────────┐      ┌──────▼────────┐
│  VM DB         │      │  VM DB        │
│  (STAGING)     │      │  (PRODUCTION) │
│                │      │               │
│ 10.x.x.20      │      │ 10.x.x.40     │
│                │      │               │
│ PostgreSQL 15  │      │ PostgreSQL 15 │
│ - simveb_      │      │ - simveb_     │
│   staging      │      │   production  │
└────────────────┘      └───────────────┘
```

### Composants

**VM App (Application):**
- Backend Laravel (API) - Port 8080
- Portal Nuxt.js - Port 3000
- Backoffice Vue - Port 3001
- Affiliate Vue - Port 3002
- Redis - Cache & Queue

**VM DB (Database):**
- PostgreSQL 15
- Bases de données séparées pour staging et production

---

## Configuration des VMs

### 1. Configuration VM App - Staging (10.x.x.10)

#### Prérequis Système

```bash
# OS: Ubuntu 22.04 LTS ou Debian 11+
# CPU: 4 cores minimum
# RAM: 8 GB minimum
# Disk: 50 GB minimum
# Network: Connexion Internet stable
```

#### Installation des Dépendances

```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Installation Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Installation des outils nécessaires
sudo apt install -y git curl wget rsync postgresql-client

# Redémarrer pour appliquer les changements de groupe
sudo reboot
```

#### Configuration Utilisateur de Déploiement

```bash
# Créer l'utilisateur simveb
sudo useradd -m -s /bin/bash simveb
sudo usermod -aG docker simveb

# Créer la structure de répertoires
sudo mkdir -p /opt/simveb
sudo chown -R simveb:simveb /opt/simveb

# Configurer SSH pour l'utilisateur simveb
sudo su - simveb
mkdir -p ~/.ssh
chmod 700 ~/.ssh
touch ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

#### Configuration SSH

```bash
# Sur votre machine locale, générer une clé SSH pour le déploiement
ssh-keygen -t ed25519 -C "gitlab-ci-simveb" -f ~/.ssh/simveb_deploy

# Afficher la clé publique
cat ~/.ssh/simveb_deploy.pub

# Ajouter la clé publique sur le serveur
# Sur le serveur, en tant que user simveb:
echo "VOTRE_CLE_PUBLIQUE_ICI" >> ~/.ssh/authorized_keys

# Tester la connexion
# Depuis votre machine locale:
ssh -i ~/.ssh/simveb_deploy simveb@10.x.x.10
```

#### Structure des Dossiers

```bash
# En tant que user simveb
cd /opt/simveb

# Créer la structure
mkdir -p {deploy/staging,deploy/production,deploy/database,backups,logs}

# Structure finale:
# /opt/simveb/
# ├── deploy/
# │   ├── staging/
# │   ├── production/
# │   └── database/
# ├── backups/
# └── logs/
```

### 2. Configuration VM App - Production (10.x.x.30)

Répéter les mêmes étapes que pour le staging.

### 3. Configuration VM DB - Staging (10.x.x.20)

#### Installation PostgreSQL

```bash
# Installer PostgreSQL 15
sudo apt update
sudo apt install -y postgresql-15 postgresql-contrib-15

# Démarrer et activer PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Vérifier l'installation
sudo systemctl status postgresql
```

#### Configuration PostgreSQL

```bash
# Se connecter en tant qu'utilisateur postgres
sudo -u postgres psql

-- Créer l'utilisateur de base de données
CREATE USER simveb WITH PASSWORD 'VOTRE_MOT_DE_PASSE_SECURISE';

-- Créer la base de données
CREATE DATABASE simveb_staging OWNER simveb;

-- Accorder tous les privilèges
GRANT ALL PRIVILEGES ON DATABASE simveb_staging TO simveb;

-- Quitter psql
\q
```

#### Configuration Accès Distant

```bash
# Éditer postgresql.conf
sudo nano /etc/postgresql/15/main/postgresql.conf

# Modifier:
listen_addresses = '*'  # ou '10.x.x.10,10.x.x.30' pour plus de sécurité

# Éditer pg_hba.conf
sudo nano /etc/postgresql/15/main/pg_hba.conf

# Ajouter à la fin:
# Autoriser les connexions depuis les VMs App
host    simveb_staging    simveb    10.x.x.10/32    scram-sha-256
host    simveb_production simveb    10.x.x.30/32    scram-sha-256

# Redémarrer PostgreSQL
sudo systemctl restart postgresql

# Vérifier que PostgreSQL écoute
sudo netstat -tulpn | grep 5432
```

#### Firewall

```bash
# Autoriser PostgreSQL uniquement depuis les VMs App
sudo ufw allow from 10.x.x.10 to any port 5432
sudo ufw allow from 10.x.x.30 to any port 5432

# Activer le firewall
sudo ufw enable
```

### 4. Configuration VM DB - Production (10.x.x.40)

```bash
# Répéter les étapes de la VM DB Staging avec:
CREATE DATABASE simveb_production OWNER simveb;
GRANT ALL PRIVILEGES ON DATABASE simveb_production TO simveb;
```

---

## Configuration GitLab CI/CD

### 1. Créer les Branches

```bash
# Depuis votre dépôt local
git checkout -b staging
git push -u origin staging

# La branche main existe déjà normalement
```

### 2. Configurer les Variables CI/CD

Allez dans GitLab: **Settings > CI/CD > Variables**

#### Variables Générales

| Variable | Valeur | Protected | Masked |
|----------|--------|-----------|--------|
| `SSH_PRIVATE_KEY` | Contenu de `~/.ssh/simveb_deploy` | ✅ | ✅ |
| `DEPLOY_USER` | `simveb` | ❌ | ❌ |
| `CI_DEPLOY_USER` | Votre username GitLab | ❌ | ✅ |
| `CI_DEPLOY_PASSWORD` | Token d'accès GitLab | ✅ | ✅ |
| `SLACK_WEBHOOK_URL` | URL du webhook Slack (optionnel) | ❌ | ✅ |

#### Variables STAGING

| Variable | Valeur | Environment | Protected |
|----------|--------|-------------|-----------|
| `DEPLOY_HOST_STAGING` | `10.x.x.10` | - | ❌ |
| `DB_HOST_STAGING` | `10.x.x.20` | - | ❌ |
| `DB_USERNAME_STAGING` | `simveb` | - | ❌ |
| `DB_PASSWORD_STAGING` | Mot de passe DB | - | ✅ |
| `REDIS_PASSWORD_STAGING` | Mot de passe Redis | - | ✅ |
| `PASSPORT_CLIENT_ID_STAGING` | ID OAuth | - | ✅ |
| `PASSPORT_CLIENT_SECRET_STAGING` | Secret OAuth | - | ✅ |
| `FEDAPAY_PUBLIC_KEY_SANDBOX` | Clé publique FedaPay sandbox | - | ❌ |
| `FEDAPAY_SECRET_KEY_SANDBOX` | Clé secrète FedaPay sandbox | - | ✅ |
| `KKIAPAY_PRIVATE_KEY_SANDBOX` | Clé privée KKiaPay sandbox | - | ✅ |
| `KKIAPAY_PUBLIC_KEY_SANDBOX` | Clé publique KKiaPay sandbox | - | ❌ |
| `KKIAPAY_SECRET_SANDBOX` | Secret KKiaPay sandbox | - | ✅ |
| `SENTRY_DSN_STAGING` | DSN Sentry staging | - | ✅ |
| `PORTAL_CLIENT_ID_STAGING` | Client ID Portal | - | ✅ |
| `PORTAL_CLIENT_SECRET_STAGING` | Client Secret Portal | - | ✅ |
| `PORTAL_SENTRY_DSN_STAGING` | DSN Sentry Portal | - | ✅ |
| `BACKOFFICE_CLIENT_ID_STAGING` | Client ID Backoffice | - | ✅ |
| `BACKOFFICE_CLIENT_SECRET_STAGING` | Client Secret Backoffice | - | ✅ |
| `BACKOFFICE_SENTRY_DSN_STAGING` | DSN Sentry Backoffice | - | ✅ |
| `AFFILIATE_CLIENT_ID_STAGING` | Client ID Affiliate | - | ✅ |
| `AFFILIATE_CLIENT_SECRET_STAGING` | Client Secret Affiliate | - | ✅ |
| `AFFILIATE_SENTRY_DSN_STAGING` | DSN Sentry Affiliate | - | ✅ |
| `MAPBOX_ACCESS_TOKEN` | Token Mapbox | - | ✅ |

#### Variables PRODUCTION

| Variable | Valeur | Environment | Protected |
|----------|--------|-------------|-----------|
| `DEPLOY_HOST_PROD` | `10.x.x.30` | production | ✅ |
| `DB_HOST_PROD` | `10.x.x.40` | production | ✅ |
| `DB_USERNAME_PROD` | `simveb` | production | ✅ |
| `DB_PASSWORD_PROD` | Mot de passe DB | production | ✅ |
| `REDIS_PASSWORD_PROD` | Mot de passe Redis fort | production | ✅ |
| `PASSPORT_CLIENT_ID_PROD` | ID OAuth | production | ✅ |
| `PASSPORT_CLIENT_SECRET_PROD` | Secret OAuth | production | ✅ |
| `FEDAPAY_PUBLIC_KEY_LIVE` | Clé publique FedaPay LIVE | production | ✅ |
| `FEDAPAY_SECRET_KEY_LIVE` | Clé secrète FedaPay LIVE | production | ✅ |
| `KKIAPAY_PRIVATE_KEY_LIVE` | Clé privée KKiaPay LIVE | production | ✅ |
| `KKIAPAY_PUBLIC_KEY_LIVE` | Clé publique KKiaPay LIVE | production | ✅ |
| `KKIAPAY_SECRET_LIVE` | Secret KKiaPay LIVE | production | ✅ |
| `SENTRY_DSN_PROD` | DSN Sentry production | production | ✅ |
| `PORTAL_CLIENT_ID_PROD` | Client ID Portal | production | ✅ |
| `PORTAL_CLIENT_SECRET_PROD` | Client Secret Portal | production | ✅ |
| `PORTAL_SENTRY_DSN_PROD` | DSN Sentry Portal | production | ✅ |
| `BACKOFFICE_CLIENT_ID_PROD` | Client ID Backoffice | production | ✅ |
| `BACKOFFICE_CLIENT_SECRET_PROD` | Client Secret Backoffice | production | ✅ |
| `BACKOFFICE_SENTRY_DSN_PROD` | DSN Sentry Backoffice | production | ✅ |
| `AFFILIATE_CLIENT_ID_PROD` | Client ID Affiliate | production | ✅ |
| `AFFILIATE_CLIENT_SECRET_PROD` | Client Secret Affiliate | production | ✅ |
| `AFFILIATE_SENTRY_DSN_PROD` | DSN Sentry Affiliate | production | ✅ |

### 3. Configurer le Token GitLab

Pour permettre au CI/CD de pull les images Docker:

1. Aller dans **Settings > Access Tokens**
2. Créer un token avec les scopes: `read_registry`, `write_registry`
3. Copier le token dans `CI_DEPLOY_PASSWORD`

---

## Configuration des Branches

### Stratégie de Branches

```
main/master  → Production (déploiement manuel)
staging      → Staging (déploiement automatique)
develop      → Développement local (pas de déploiement)
feature/*    → Features (pas de déploiement)
```

### Workflow Git

```bash
# 1. Développement sur feature branch
git checkout -b feature/nouvelle-fonctionnalite
# ... développement ...
git commit -m "feat: ajouter nouvelle fonctionnalité"
git push origin feature/nouvelle-fonctionnalite

# 2. Merge vers develop
git checkout develop
git merge feature/nouvelle-fonctionnalite
git push origin develop

# 3. Déployer sur staging
git checkout staging
git merge develop
git push origin staging  # ✅ Déclenchement automatique du déploiement staging

# 4. Tests sur staging
# ... tests et validation ...

# 5. Déployer en production
git checkout main
git merge staging
git push origin main  # ⚠️ Déploiement manuel requis dans GitLab
```

---

## Déploiement

### Déploiement Initial

#### 1. Préparer les Serveurs

```bash
# Sur VM App Staging (10.x.x.10)
ssh simveb@10.x.x.10

# Cloner le repository (optionnel, le CI/CD sync les fichiers)
# Ou créer manuellement la structure
mkdir -p /opt/simveb/{deploy/{staging,production,database},backups,logs}
```

#### 2. Initialiser la Base de Données

```bash
# Sur VM DB Staging (10.x.x.20)
ssh simveb@10.x.x.20  # ou connexion directe

# Vérifier que PostgreSQL est configuré
psql -U simveb -d simveb_staging -h localhost -c "SELECT version();"
```

#### 3. Premier Déploiement Staging

```bash
# Sur votre machine locale
git checkout staging
git push origin staging

# Dans GitLab CI/CD > Pipelines
# Le pipeline se lancera automatiquement
# Vérifier les logs de chaque job
```

#### 4. Vérification Post-Déploiement

```bash
# Sur VM App Staging
ssh simveb@10.x.x.10

# Vérifier les conteneurs
docker ps

# Vérifier les logs
docker logs simveb-backend-staging
docker logs simveb-portal-staging

# Tester les services
curl http://localhost:8080/health
curl http://localhost:3000
curl http://localhost:3001
curl http://localhost:3002
```

### Déploiement en Production

```bash
# 1. Merger staging vers main
git checkout main
git merge staging
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin main --tags

# 2. Dans GitLab CI/CD > Pipelines
# Cliquer sur le pipeline de la branche main
# Cliquer sur le bouton "Play" du job deploy:production
# Confirmer le déploiement

# 3. Surveiller les logs
# Le déploiement prendra environ 2-5 minutes
# Un backup automatique sera créé avant le déploiement
```

### Rollback

En cas de problème en production:

```bash
# Dans GitLab CI/CD > Pipelines
# Cliquer sur le pipeline de production
# Cliquer sur "Play" du job rollback:production
# Confirmer le rollback

# Ou manuellement sur le serveur:
ssh simveb@10.x.x.30
cd /opt/simveb
bash deploy/production/deploy-all.sh rollback
```

---

## Monitoring et Maintenance

### Logs

```bash
# Logs des conteneurs
docker logs -f simveb-backend-staging
docker logs -f simveb-portal-staging --tail=100

# Logs Laravel
docker exec simveb-backend-staging tail -f storage/logs/laravel.log

# Logs PostgreSQL (sur VM DB)
sudo tail -f /var/log/postgresql/postgresql-15-main.log
```

### Backups

```bash
# Backup manuel
ssh simveb@10.x.x.30
cd /opt/simveb
bash deploy/database/backup-db.sh production

# Lister les backups
ls -lh /opt/simveb/backups/

# Restaurer un backup
bash deploy/database/restore-db.sh production /opt/simveb/backups/simveb_production_20250103-120000.sql.gz
```

### Monitoring des Ressources

```bash
# CPU et Mémoire
docker stats

# Espace disque
df -h

# Logs système
journalctl -u docker -f
```

### Nettoyage

```bash
# Nettoyer les images Docker inutilisées
docker system prune -a

# Nettoyer les vieux backups (garde les 30 derniers)
cd /opt/simveb/backups
ls -t simveb_production_*.sql.gz | tail -n +31 | xargs rm --
```

---

## Troubleshooting

### Problème: Le déploiement échoue avec "Permission denied"

**Solution:**

```bash
# Vérifier les permissions SSH
ssh simveb@10.x.x.10 "ls -la /opt/simveb"

# Corriger les permissions
ssh simveb@10.x.x.10 "sudo chown -R simveb:simveb /opt/simveb"
```

### Problème: Impossible de se connecter à la base de données

**Solution:**

```bash
# Tester la connexion depuis la VM App
ssh simveb@10.x.x.10
psql -h 10.x.x.20 -U simveb -d simveb_staging

# Vérifier pg_hba.conf sur la VM DB
ssh simveb@10.x.x.20
sudo cat /etc/postgresql/15/main/pg_hba.conf

# Vérifier que PostgreSQL écoute
sudo netstat -tulpn | grep 5432
```

### Problème: Le conteneur backend ne démarre pas

**Solution:**

```bash
# Voir les logs détaillés
docker logs simveb-backend-staging

# Vérifier le fichier .env
docker exec simveb-backend-staging cat .env

# Recréer le conteneur
cd /opt/simveb
docker-compose -f deploy/staging/docker-compose.yml down
docker-compose -f deploy/staging/docker-compose.yml up -d
```

### Problème: Images Docker non trouvées

**Solution:**

```bash
# Vérifier la connexion au registry
docker login registry.gitlab.com

# Re-pull les images
cd /opt/simveb
docker-compose -f deploy/staging/docker-compose.yml pull

# Vérifier que les images existent dans GitLab
# GitLab > Packages & Registries > Container Registry
```

### Problème: Health check échoue après déploiement

**Solution:**

```bash
# Attendre un peu plus (backend peut prendre 30-60s)
sleep 60
curl http://localhost:8080/health

# Vérifier que toutes les dépendances sont OK
docker exec simveb-backend-staging php artisan health:check

# Vérifier Redis
docker exec simveb-redis-staging redis-cli ping

# Vérifier la DB
docker exec simveb-backend-staging php artisan db:show
```

---

## Checklist de Déploiement

### Avant le Premier Déploiement

- [ ] VMs créées et accessibles
- [ ] Docker installé sur les VMs App
- [ ] PostgreSQL installé et configuré sur les VMs DB
- [ ] Utilisateur `simveb` créé avec les bonnes permissions
- [ ] Clés SSH configurées
- [ ] Variables GitLab CI/CD configurées
- [ ] Branches `staging` et `main` créées
- [ ] Connexion DB testée depuis VM App vers VM DB
- [ ] Firewall configuré

### Avant Chaque Déploiement Production

- [ ] Tests passent sur staging
- [ ] Smoke tests effectués sur staging
- [ ] Backup de production créé
- [ ] Équipe notifiée du déploiement
- [ ] Plan de rollback prêt
- [ ] Monitoring vérifié

### Après Chaque Déploiement

- [ ] Health checks passent
- [ ] Logs vérifiés (pas d'erreurs)
- [ ] Tests smoke en production
- [ ] Monitoring vérifié
- [ ] Équipe notifiée du succès

---

## Support

Pour toute question ou problème:

1. Consulter les logs: `docker logs <container_name>`
2. Vérifier la documentation: `/docs/`
3. Contacter l'équipe DevOps

---

**Document créé le:** 2026-01-03
**Version:** 1.0
**Projet:** SIMVEB - Système de Déploiement CI/CD
