# Scripts de Déploiement SIMVEB pour Windows

Ce dossier contient les scripts PowerShell pour automatiser le déploiement et la gestion du projet SIMVEB sur Windows.

## 📋 Liste des Scripts

### Scripts de Déploiement

| Script | Description | Usage |
|--------|-------------|-------|
| `deploy-backend.ps1` | Déploie le backend Laravel avec Docker | `.\deploy-backend.ps1` |
| `deploy-portal.ps1` | Configure le Portal Nuxt.js | `.\deploy-portal.ps1` |
| `deploy-backoffice.ps1` | Configure le Backoffice Vuero | `.\deploy-backoffice.ps1` |
| `deploy-affiliate.ps1` | Configure l'Affiliate | `.\deploy-affiliate.ps1` |

### Script de Gestion

| Script | Description | Usage |
|--------|-------------|-------|
| `manage-services.ps1` | Gère les services (démarrer, arrêter, logs) | `.\manage-services.ps1` |

## 🚀 Démarrage Rapide

### 1. Configuration Initiale

```powershell
# Autoriser l'exécution des scripts PowerShell
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force

# Naviguer vers le dossier scripts
cd scripts
```

### 2. Déployer le Backend

```powershell
.\deploy-backend.ps1
```

Ce script va :
- ✓ Vérifier que Docker est en cours d'exécution
- ✓ Créer le fichier `.env` depuis `.env.example`
- ✓ Démarrer les conteneurs Docker (PostgreSQL, PHP, Nginx)
- ✓ Installer les dépendances Composer
- ✓ Exécuter les migrations de base de données
- ✓ Peupler la base de données
- ✓ Installer Laravel Passport

**Backend disponible sur** : http://localhost:8004

### 3. Déployer un Frontend

#### Portal

```powershell
.\deploy-portal.ps1
```

Pour démarrer automatiquement après la configuration :
```powershell
.\deploy-portal.ps1 -Start
```

#### Backoffice

```powershell
.\deploy-backoffice.ps1
```

Par défaut, le Backoffice utilise le port 3001 (pour éviter le conflit avec le Portal).

Pour utiliser un port différent :
```powershell
.\deploy-backoffice.ps1 -Port 3002
```

#### Affiliate

```powershell
.\deploy-affiliate.ps1
```

## 📊 Gestion des Services

### Menu Interactif

```powershell
.\manage-services.ps1
```

Ce script affiche un menu pour :
- Voir le statut de tous les services
- Arrêter tous les services
- Redémarrer le backend
- Voir les logs
- Nettoyer Docker

### Commandes Directes

```powershell
# Voir le statut
.\manage-services.ps1 -Action status

# Arrêter tous les services
.\manage-services.ps1 -Action stop -Service all

# Arrêter le backend uniquement
.\manage-services.ps1 -Action stop -Service backend

# Redémarrer le backend
.\manage-services.ps1 -Action restart -Service backend

# Voir les logs du backend
.\manage-services.ps1 -Action logs -Service backend
```

## 🔧 Paramètres des Scripts

### deploy-backend.ps1

```powershell
# Ignorer la création du fichier .env
.\deploy-backend.ps1 -SkipEnv
```

### deploy-portal.ps1

```powershell
# Ignorer la création du fichier .env
.\deploy-portal.ps1 -SkipEnv

# Démarrer automatiquement après la configuration
.\deploy-portal.ps1 -Start
```

### deploy-backoffice.ps1

```powershell
# Spécifier un port personnalisé
.\deploy-backoffice.ps1 -Port 3002

# Démarrer automatiquement
.\deploy-backoffice.ps1 -Start

# Combiner les options
.\deploy-backoffice.ps1 -Port 3002 -Start -SkipEnv
```

### deploy-affiliate.ps1

```powershell
# Démarrer automatiquement
.\deploy-affiliate.ps1 -Start
```

## 📁 Structure des Composants

```
projets01/
├── simveb-backend-develop/          # Backend Laravel
│   ├── docker-compose.yml
│   ├── .env
│   └── ...
├── simveb-portal-design-develop/    # Portal Nuxt.js
│   ├── .env
│   └── ...
├── simveb-backoffice-develop/       # Backoffice Vuero
│   ├── .env
│   └── ...
├── simveb-affiliate-develop/        # Affiliate
│   ├── .env
│   └── ...
└── scripts/                         # Ce dossier
    ├── deploy-backend.ps1
    ├── deploy-portal.ps1
    ├── deploy-backoffice.ps1
    ├── deploy-affiliate.ps1
    └── manage-services.ps1
```

## 🔄 Workflow Typique

### Première Installation

```powershell
# 1. Déployer le backend
.\deploy-backend.ps1

# 2. Déployer le Portal
.\deploy-portal.ps1

# 3. Dans un nouveau terminal, démarrer le Portal
cd ..\simveb-portal-design-develop
npm run dev

# 4. (Optionnel) Déployer et démarrer le Backoffice
cd ..\scripts
.\deploy-backoffice.ps1 -Port 3001 -Start
```

### Développement Quotidien

```powershell
# Démarrer le backend
cd simveb-backend-develop
docker-compose up -d

# Dans des terminaux séparés, démarrer les frontends nécessaires
cd simveb-portal-design-develop
npm run dev

# OU
cd simveb-backoffice-develop
pnpm dev -- --port 3001

# OU
cd simveb-affiliate-develop
pnpm dev
```

### Arrêt des Services

```powershell
# Option 1: Script automatique
cd scripts
.\manage-services.ps1 -Action stop -Service all

# Option 2: Manuel
# - Appuyer sur Ctrl+C dans chaque terminal frontend
# - Arrêter Docker
cd simveb-backend-develop
docker-compose down
```

## ⚠️ Résolution de Problèmes

### Script ne s'exécute pas

**Erreur** : `cannot be loaded because running scripts is disabled`

**Solution** :
```powershell
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force
```

### Docker n'est pas démarré

**Erreur** : `Cannot connect to the Docker daemon`

**Solution** :
- Démarrer Docker Desktop
- Attendre que Docker soit complètement démarré (icône verte)
- Réexécuter le script

### Port déjà utilisé

**Erreur** : `port is already allocated`

**Solution** :
```powershell
# Trouver le processus utilisant le port
netstat -ano | findstr :8004

# Tuer le processus
taskkill /PID <PID> /F

# OU utiliser le script de gestion
.\manage-services.ps1 -Action stop -Service all
```

### pnpm non trouvé

**Erreur** : `pnpm : The term 'pnpm' is not recognized`

**Solution** :
```powershell
# Installer pnpm globalement
npm install -g pnpm

# Redémarrer le terminal
```

### Erreurs de dépendances

**Solution** :
```powershell
# Backend
cd simveb-backend-develop
docker-compose exec app composer install

# Portal
cd simveb-portal-design-develop
Remove-Item -Recurse -Force node_modules
Remove-Item -Force package-lock.json
npm install

# Backoffice/Affiliate
Remove-Item -Recurse -Force node_modules
Remove-Item -Force pnpm-lock.yaml
pnpm install
```

## 🔍 Vérification de l'Installation

### Checklist

```powershell
# 1. Vérifier Docker
docker --version
docker ps

# 2. Vérifier Node.js
node --version

# 3. Vérifier npm
npm --version

# 4. Vérifier pnpm
pnpm --version

# 5. Vérifier le statut des services
cd scripts
.\manage-services.ps1 -Action status
```

### URLs d'Accès

| Service | URL | Statut |
|---------|-----|--------|
| Backend API | http://localhost:8004 | ✓ |
| Portal | http://localhost:3000 | ✓ |
| Backoffice | http://localhost:3001 | ✓ |
| Affiliate | http://localhost:5173 | ✓ |

## 📝 Commandes Utiles

### Backend (Docker)

```powershell
cd simveb-backend-develop

# Voir les logs
docker-compose logs -f app

# Accéder au conteneur
docker-compose exec app bash

# Exécuter des commandes Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Redémarrer les services
docker-compose restart

# Arrêter et supprimer les volumes
docker-compose down -v
```

### Frontends

```powershell
# Portal
cd simveb-portal-design-develop
npm run dev          # Développement
npm run build        # Production
npm run start        # Serveur production

# Backoffice
cd simveb-backoffice-develop
pnpm dev -- --port 3001    # Développement
pnpm build                 # Production
pnpm preview               # Prévisualiser le build

# Affiliate
cd simveb-affiliate-develop
pnpm dev             # Développement
pnpm build           # Production
pnpm preview         # Prévisualiser le build
```

## 🔐 Sécurité

### Fichiers .env

⚠️ **IMPORTANT** : Les fichiers `.env` contiennent des informations sensibles.

- Ne jamais commiter les fichiers `.env` dans Git
- Utiliser `.env.example` comme modèle
- Changer les mots de passe par défaut en production

### Mots de Passe par Défaut

En développement local, les scripts utilisent :
- **PostgreSQL** : `simveb` / `password`
- **Laravel APP_KEY** : Généré automatiquement

⚠️ **Changez ces valeurs en production !**

## 📚 Ressources

### Documentation Officielle

- [Laravel](https://laravel.com/docs)
- [Nuxt.js](https://nuxt.com/docs)
- [Vue.js](https://vuejs.org/guide/)
- [Docker](https://docs.docker.com/)
- [PowerShell](https://docs.microsoft.com/powershell/)

### Support

Pour toute question ou problème :
1. Consultez le fichier `DEPLOYMENT_WINDOWS.md` à la racine du projet
2. Vérifiez les logs avec `.\manage-services.ps1 -Action logs`
3. Consultez la documentation officielle des technologies utilisées

## 🎯 Prochaines Étapes

Après avoir déployé avec succès :

1. **Configurer votre IDE** (VS Code recommandé)
2. **Installer les extensions** :
   - PHP Intelephense
   - Vue Language Features (Volar)
   - Docker
   - ESLint
   - Prettier
3. **Consulter la documentation** de chaque composant
4. **Commencer à développer !** 🚀

---

**Bon développement avec SIMVEB !** 💙
