# Guide Rapide de Déploiement SIMVEB

## 🚀 Démarrage Rapide

Ce guide vous permet de déployer rapidement SIMVEB sur vos deux VMs avec GitLab CI/CD.

## 📋 Prérequis

### Infrastructure

- ✅ **2 VMs pour Staging:**
  - VM App Staging (10.x.x.10) - 4 CPU, 8GB RAM, 50GB disk
  - VM DB Staging (10.x.x.20) - 2 CPU, 4GB RAM, 50GB disk

- ✅ **2 VMs pour Production:**
  - VM App Production (10.x.x.30) - 4 CPU, 8GB RAM, 100GB disk
  - VM DB Production (10.x.x.40) - 4 CPU, 8GB RAM, 100GB disk

### Logiciels

- Ubuntu 22.04 LTS ou Debian 11+
- Docker & Docker Compose (sur VMs App)
- PostgreSQL 15 (sur VMs DB)
- GitLab (repo hébergé)

## 🎯 Configuration en 5 Étapes

### Étape 1: Préparer les VMs

```bash
# Sur chaque VM App (staging et production)
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
sudo apt install -y git rsync postgresql-client

# Sur chaque VM DB (staging et production)
sudo apt install -y postgresql-15
```

### Étape 2: Créer l'Utilisateur de Déploiement

```bash
# Sur toutes les VMs
sudo useradd -m -s /bin/bash simveb
sudo usermod -aG docker simveb  # Sur VMs App seulement
sudo mkdir -p /opt/simveb
sudo chown simveb:simveb /opt/simveb
```

### Étape 3: Configurer SSH

```bash
# Sur votre machine locale
ssh-keygen -t ed25519 -C "gitlab-ci-simveb" -f ~/.ssh/simveb_deploy

# Afficher et copier la clé publique
cat ~/.ssh/simveb_deploy.pub

# Sur chaque VM, en tant que user simveb
sudo su - simveb
mkdir -p ~/.ssh && chmod 700 ~/.ssh
echo "VOTRE_CLE_PUBLIQUE" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### Étape 4: Configurer PostgreSQL

```bash
# Sur VM DB Staging (10.x.x.20)
sudo -u postgres psql
CREATE USER simveb WITH PASSWORD 'mot_de_passe_securise';
CREATE DATABASE simveb_staging OWNER simveb;
GRANT ALL PRIVILEGES ON DATABASE simveb_staging TO simveb;
\q

# Configurer l'accès distant
sudo nano /etc/postgresql/15/main/postgresql.conf
# Modifier: listen_addresses = '*'

sudo nano /etc/postgresql/15/main/pg_hba.conf
# Ajouter: host simveb_staging simveb 10.x.x.10/32 scram-sha-256

sudo systemctl restart postgresql
sudo ufw allow from 10.x.x.10 to any port 5432

# Répéter pour Production (VM DB 10.x.x.40)
```

### Étape 5: Configurer GitLab CI/CD

1. **Créer la branche staging:**
   ```bash
   git checkout -b staging
   git push -u origin staging
   ```

2. **Configurer les variables dans GitLab:**
   - Aller dans `Settings > CI/CD > Variables`
   - Ajouter toutes les variables listées dans `docs/SETUP_DEPLOYMENT.md`

3. **Variables minimales requises:**

   ```
   SSH_PRIVATE_KEY         = Contenu de ~/.ssh/simveb_deploy
   DEPLOY_USER            = simveb

   # Staging
   DEPLOY_HOST_STAGING    = 10.x.x.10
   DB_HOST_STAGING        = 10.x.x.20
   DB_USERNAME_STAGING    = simveb
   DB_PASSWORD_STAGING    = votre_mot_de_passe

   # Production
   DEPLOY_HOST_PROD       = 10.x.x.30
   DB_HOST_PROD           = 10.x.x.40
   DB_USERNAME_PROD       = simveb
   DB_PASSWORD_PROD       = votre_mot_de_passe
   ```

## 🔄 Workflow de Déploiement

### Déploiement Staging (Automatique)

```bash
# 1. Développer sur une branche feature
git checkout -b feature/ma-fonctionnalite
# ... développement ...
git commit -m "feat: nouvelle fonctionnalité"
git push origin feature/ma-fonctionnalite

# 2. Merger vers staging
git checkout staging
git merge feature/ma-fonctionnalite
git push origin staging

# ✅ Le déploiement sur staging se lance automatiquement !
```

### Déploiement Production (Manuel)

```bash
# 1. Merger staging vers main
git checkout main
git merge staging
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin main --tags

# 2. Dans GitLab: CI/CD > Pipelines
# 3. Cliquer sur "Play" pour deploy:production
# 4. Confirmer le déploiement

# ✅ Le déploiement production démarre après confirmation
```

## 📊 Architecture de Déploiement

```
staging branch → Build & Test → Deploy Auto → VM Staging
main branch    → Build & Test → Deploy Manuel → VM Production
```

### Branches

- `main` → Production (déploiement manuel)
- `staging` → Staging (déploiement automatique)
- `develop` → Développement local
- `feature/*` → Features

## 🌐 URLs d'Accès

### Staging

| Service | URL |
|---------|-----|
| Backend API | `https://staging-api.simveb-bj.com` |
| Portal | `https://staging.simveb-bj.com` |
| Backoffice | `https://staging-admin.simveb-bj.com` |
| Affiliate | `https://staging-affiliate.simveb-bj.com` |

### Production

| Service | URL |
|---------|-----|
| Backend API | `https://api.simveb-bj.com` |
| Portal | `https://simveb-bj.com` |
| Backoffice | `https://admin.simveb-bj.com` |
| Affiliate | `https://affiliate.simveb-bj.com` |

## 🛠️ Scripts Disponibles

### Sur le Serveur

```bash
# Déployer
bash /opt/simveb/deploy/staging/deploy-all.sh

# Voir le statut
bash /opt/simveb/deploy/staging/deploy-all.sh status

# Faire un backup
bash /opt/simveb/deploy/database/backup-db.sh staging

# Rollback (production)
bash /opt/simveb/deploy/production/deploy-all.sh rollback
```

## 📚 Documentation Complète

- **Configuration détaillée:** `docs/SETUP_DEPLOYMENT.md`
- **Scripts de déploiement:** `deploy/README.md`
- **CI/CD:** `CI_CD_DOCUMENTATION.md`

## ✅ Checklist Première Installation

### Sur les VMs

- [ ] Docker installé (VMs App)
- [ ] PostgreSQL installé (VMs DB)
- [ ] Utilisateur `simveb` créé
- [ ] SSH configuré
- [ ] Bases de données créées
- [ ] Accès réseau configuré (pg_hba.conf)
- [ ] Firewall configuré

### Dans GitLab

- [ ] Branche `staging` créée
- [ ] Variables CI/CD configurées
- [ ] Token d'accès GitLab créé
- [ ] SSH_PRIVATE_KEY ajouté

### Test de Connexion

```bash
# Tester SSH
ssh -i ~/.ssh/simveb_deploy simveb@10.x.x.10
ssh -i ~/.ssh/simveb_deploy simveb@10.x.x.30

# Tester PostgreSQL depuis VM App
psql -h 10.x.x.20 -U simveb -d simveb_staging
psql -h 10.x.x.40 -U simveb -d simveb_production
```

## 🆘 Support

### Problèmes Courants

**SSH ne fonctionne pas:**
```bash
# Vérifier les permissions
chmod 600 ~/.ssh/simveb_deploy
ssh -v simveb@10.x.x.10
```

**PostgreSQL refuse la connexion:**
```bash
# Vérifier pg_hba.conf
sudo cat /etc/postgresql/15/main/pg_hba.conf
# Redémarrer PostgreSQL
sudo systemctl restart postgresql
```

**Docker ne démarre pas:**
```bash
# Vérifier le service
sudo systemctl status docker
sudo systemctl start docker
```

### Contacts

- 📖 Documentation: `/docs/`
- 🐛 Issues: GitLab Issues
- 💬 Support: devops@simveb-bj.com

## 🎉 Félicitations !

Votre infrastructure CI/CD est maintenant prête. Chaque push sur `staging` déploiera automatiquement, et les déploiements en production sont sécurisés avec confirmation manuelle et rollback automatique.

---

**Version:** 1.0
**Date:** 2026-01-03
**Projet:** SIMVEB - CI/CD avec GitLab
