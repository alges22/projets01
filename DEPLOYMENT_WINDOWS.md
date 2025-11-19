# Guide de Déploiement SIMVEB sur Windows

Ce guide détaille le processus complet de déploiement local du projet SIMVEB sur un ordinateur Windows.

## 📋 Vue d'ensemble

SIMVEB est un système multi-composants comprenant :

| Composant | Technologie | Port | Description |
|-----------|-------------|------|-------------|
| **Backend API** | Laravel 10 (PHP 8.2+) | 8004 | API REST avec authentification OAuth2 |
| **Portal** | Nuxt.js 3 (Vue 3) | 3000 | Portail public |
| **Backoffice** | Vue 3 + Vuero | 3000 | Interface d'administration |
| **Affiliate** | Vue 3 + Vite | 5173 | Portail affilié |
| **Base de données** | PostgreSQL | 5432 | Base de données principale |

---

## 🔧 Prérequis

### Logiciels requis

#### 1. Docker Desktop for Windows ⭐ (Recommandé)
- **Télécharger** : https://www.docker.com/products/docker-desktop
- **Configuration** :
  - Activer WSL 2 backend
  - Allouer au moins 4 GB de RAM
  - Partager les disques nécessaires (Settings > Resources > File Sharing)

#### 2. Node.js (LTS)
- **Version requise** : 18.x ou 20.x
- **Télécharger** : https://nodejs.org/
- **Vérifier l'installation** :
  ```powershell
  node --version  # Doit afficher v18.x.x ou v20.x.x
  npm --version
  ```

#### 3. pnpm (Gestionnaire de paquets)
- **Installation** :
  ```powershell
  npm install -g pnpm@latest
  ```
- **Vérifier** :
  ```powershell
  pnpm --version
  ```

#### 4. Git for Windows
- **Télécharger** : https://git-scm.com/download/win
- **Configuration recommandée** :
  ```powershell
  git config --global core.autocrlf input
  ```

### Logiciels optionnels

- **PostgreSQL** (si vous n'utilisez pas Docker) : https://www.postgresql.org/download/windows/
- **PHP 8.2+** (si vous n'utilisez pas Docker) : https://windows.php.net/download/
- **Composer** (si vous n'utilisez pas Docker) : https://getcomposer.org/download/

---

## 🚀 Déploiement Automatique (Méthode Recommandée)

### Option 1 : Script PowerShell Tout-en-Un

1. **Ouvrir PowerShell en tant qu'administrateur**

2. **Autoriser l'exécution de scripts** :
   ```powershell
   Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force
   ```

3. **Naviguer vers le dossier du projet** :
   ```powershell
   cd C:\chemin\vers\projets01
   ```

4. **Exécuter le script de déploiement** :
   ```powershell
   .\deploy-windows.ps1
   ```

5. **Suivre le menu interactif** :
   ```
   ╔════════════════════════════════════════════════╗
   ║     SIMVEB - Déploiement Windows              ║
   ╚════════════════════════════════════════════════╝

   [1] Déploiement complet (Backend + tous les frontends)
   [2] Backend uniquement
   [3] Portal uniquement
   [4] Backoffice uniquement
   [5] Affiliate uniquement
   [6] Vérifier les prérequis
   [7] Arrêter tous les services
   [8] Voir les logs
   [Q] Quitter
   ```

### Option 2 : Scripts Individuels

Vous pouvez également exécuter chaque composant séparément :

```powershell
# Backend
.\scripts\deploy-backend.ps1

# Portal
.\scripts\deploy-portal.ps1

# Backoffice
.\scripts\deploy-backoffice.ps1

# Affiliate
.\scripts\deploy-affiliate.ps1
```

---

## 📝 Déploiement Manuel (Étape par Étape)

### Étape 1 : Backend API (Laravel)

#### 1.1 Préparation

```powershell
# Naviguer vers le backend
cd simveb-backend-develop

# Copier le fichier d'environnement
Copy-Item .env.example .env
```

#### 1.2 Configuration du fichier .env

Éditez le fichier `.env` avec les valeurs suivantes :

```env
APP_NAME=SimVeb
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8004

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=simveb
DB_USERNAME=simveb
DB_PASSWORD=password

PORTAL_URL=http://localhost:3000
ADMIN_URL=http://localhost:3000
AFFILIATE_URL=http://localhost:5173
```

#### 1.3 Démarrage avec Docker

```powershell
# Démarrer les conteneurs (PostgreSQL + PHP + Nginx)
docker-compose up -d

# Vérifier que les conteneurs sont en cours d'exécution
docker-compose ps

# Installer les dépendances PHP
docker-compose exec app composer install

# Générer la clé d'application
docker-compose exec app php artisan key:generate

# Créer le lien symbolique pour le stockage
docker-compose exec app php artisan storage:link

# Exécuter les migrations de base de données
docker-compose exec app php artisan migrate

# Peupler la base de données
docker-compose exec app php artisan db:seed

# Installer Laravel Passport (OAuth2)
docker-compose exec app php artisan passport:install
```

#### 1.4 Vérification

```powershell
# Vérifier que le backend répond
curl http://localhost:8004/api/health

# Voir les logs
docker-compose logs -f app
```

✅ **Backend disponible sur** : http://localhost:8004

---

### Étape 2 : Portal (Nuxt.js)

#### 2.1 Préparation

```powershell
# Naviguer vers le portal
cd ..\simveb-portal-design-develop

# Copier le fichier d'environnement
Copy-Item .env.example .env
```

#### 2.2 Configuration du fichier .env

```env
VITE_API_URL=http://localhost:8004/api
VITE_PORTAL_URL=http://localhost:3000
VITE_ADMIN_URL=http://localhost:3000
VITE_AFFILIATE_URL=http://localhost:5173
```

#### 2.3 Installation et démarrage

```powershell
# Installer les dépendances Node.js
npm install

# Démarrer le serveur de développement
npm run dev

# OU pour la production
npm run build
npm run start
```

✅ **Portal disponible sur** : http://localhost:3000

---

### Étape 3 : Backoffice (Vuero)

#### 3.1 Préparation

```powershell
# Naviguer vers le backoffice
cd ..\simveb-backoffice-develop

# Copier le fichier d'environnement
Copy-Item .env.example .env
```

#### 3.2 Configuration du fichier .env

```env
VITE_API_URL=http://localhost:8004/api
VITE_ADMIN_URL=http://localhost:3000
```

#### 3.3 Installation et démarrage

⚠️ **Important** : Le backoffice nécessite **pnpm** (pas npm ou yarn)

```powershell
# Installer les dépendances avec pnpm
pnpm install

# Démarrer le serveur de développement (port 3000)
pnpm dev

# OU sur un port différent pour éviter les conflits
pnpm dev -- --port 3001

# OU pour la production
pnpm build
pnpm preview
```

✅ **Backoffice disponible sur** : http://localhost:3000 (ou 3001)

---

### Étape 4 : Affiliate

#### 4.1 Préparation

```powershell
# Naviguer vers affiliate
cd ..\simveb-affiliate-develop

# Copier le fichier d'environnement
Copy-Item .env.example .env
```

#### 4.2 Configuration du fichier .env

```env
VITE_API_URL=http://localhost:8004/api
VITE_ADMIN_URL=http://localhost:3000
VITE_AFFILIATE_URL=http://localhost:5173
```

#### 4.3 Installation et démarrage

```powershell
# Installer les dépendances avec pnpm
pnpm install

# Démarrer le serveur de développement
pnpm dev

# OU pour la production
pnpm build
pnpm preview
```

✅ **Affiliate disponible sur** : http://localhost:5173

---

## 🗂️ Import de la base de données

Si vous souhaitez importer le dump existant (`simvebbase (1).sql`) :

### Avec Docker

```powershell
# Naviguer vers le dossier contenant le fichier SQL
cd C:\chemin\vers\projets01

# Importer dans le conteneur PostgreSQL
Get-Content "simvebbase (1).sql" | docker-compose -f simveb-backend-develop\docker-compose.yml exec -T db psql -U simveb -d simveb
```

### Avec PostgreSQL natif

```powershell
psql -U simveb -d simveb -f "simvebbase (1).sql"
```

---

## 🔄 Workflow de Développement Quotidien

### Démarrage complet

```powershell
# Terminal 1 - Backend
cd simveb-backend-develop
docker-compose up

# Terminal 2 - Portal (si nécessaire)
cd simveb-portal-design-develop
npm run dev

# Terminal 3 - Backoffice (si nécessaire)
cd simveb-backoffice-develop
pnpm dev -- --port 3001

# Terminal 4 - Affiliate (si nécessaire)
cd simveb-affiliate-develop
pnpm dev
```

### Arrêt des services

```powershell
# Arrêter le backend Docker
cd simveb-backend-develop
docker-compose down

# Arrêter les frontends
# Appuyez sur Ctrl+C dans chaque terminal
```

### Voir les logs

```powershell
# Logs du backend
cd simveb-backend-develop
docker-compose logs -f app

# Logs de la base de données
docker-compose logs -f db

# Logs Nginx
docker-compose logs -f nginx
```

---

## ⚠️ Résolution des Problèmes Courants

### 1. Conflit de ports (Portal et Backoffice utilisent le port 3000)

**Symptôme** : `Error: listen EADDRINUSE: address already in use :::3000`

**Solution** :
```powershell
# Modifier le port du Backoffice
pnpm dev -- --port 3001

# OU modifier package.json
# "dev": "vite --port 3001"
```

### 2. Docker Desktop ne démarre pas

**Symptôme** : `Cannot connect to the Docker daemon`

**Solutions** :
- Redémarrer Docker Desktop
- Vérifier que WSL 2 est activé
- Exécuter PowerShell en tant qu'administrateur
- Réinitialiser Docker Desktop (Settings > Troubleshoot > Reset to factory defaults)

### 3. Erreurs de permissions Docker

**Symptôme** : `Permission denied` lors de l'accès aux fichiers

**Solutions** :
- Vérifier le partage de fichiers : Docker Desktop > Settings > Resources > File Sharing
- Ajouter le dossier du projet à la liste des dossiers partagés
- Redémarrer Docker Desktop

### 4. pnpm non reconnu

**Symptôme** : `pnpm : The term 'pnpm' is not recognized`

**Solutions** :
```powershell
# Réinstaller pnpm
npm install -g pnpm

# Vérifier la variable PATH
$env:Path

# Redémarrer le terminal

# OU utiliser npx
npx pnpm install
npx pnpm dev
```

### 5. Erreurs de migration de base de données

**Symptôme** : `SQLSTATE[42P01]: Undefined table`

**Solutions** :
```powershell
# Réinitialiser la base de données
cd simveb-backend-develop
docker-compose exec app php artisan migrate:fresh --seed

# Si cela ne fonctionne pas, recréer les conteneurs
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed
```

### 6. Erreurs de dépendances Node.js

**Symptôme** : `Cannot find module` ou erreurs lors de `npm install`

**Solutions** :
```powershell
# Supprimer node_modules et le cache
Remove-Item -Recurse -Force node_modules
Remove-Item -Force package-lock.json  # ou pnpm-lock.yaml

# Réinstaller
npm install  # ou pnpm install

# Si cela persiste, nettoyer le cache
npm cache clean --force
pnpm store prune
```

### 7. Erreurs CORS (Cross-Origin Resource Sharing)

**Symptôme** : `Access to fetch at 'http://localhost:8004/api/...' has been blocked by CORS policy`

**Solutions** :
- Vérifier que le backend est démarré
- Vérifier la configuration CORS dans `simveb-backend-develop/config/cors.php`
- Vérifier que les URL dans les fichiers `.env` sont correctes

### 8. Erreurs de fin de ligne (CRLF vs LF)

**Symptôme** : Scripts bash ne fonctionnent pas dans Docker

**Solutions** :
```powershell
# Configurer Git pour utiliser LF
git config --global core.autocrlf input

# Rerecréer les fichiers
git rm --cached -r .
git reset --hard
```

### 9. Port 8004 déjà utilisé

**Symptôme** : `Bind for 0.0.0.0:8004 failed: port is already allocated`

**Solutions** :
```powershell
# Trouver le processus utilisant le port
netstat -ano | findstr :8004

# Tuer le processus (remplacer PID par le numéro trouvé)
taskkill /PID <PID> /F

# OU modifier le port dans docker-compose.yml
# ports:
#   - "8005:80"  # Utiliser le port 8005 au lieu de 8004
```

### 10. Erreurs SSL/HTTPS en développement

**Symptôme** : Avertissements de certificat SSL

**Solutions** :
- En développement, les certificats auto-signés sont normaux
- Accepter l'exception dans le navigateur
- Ou désactiver la vérification SSL (non recommandé en production)

---

## 📊 Vérification de l'Installation

### Checklist complète

- [ ] Docker Desktop est démarré
- [ ] Les conteneurs backend sont en cours d'exécution : `docker-compose ps`
- [ ] Backend répond : http://localhost:8004/api/health
- [ ] Base de données accessible : `docker-compose exec db psql -U simveb -d simveb -c "\dt"`
- [ ] Portal accessible : http://localhost:3000
- [ ] Backoffice accessible : http://localhost:3000 ou 3001
- [ ] Affiliate accessible : http://localhost:5173
- [ ] Pas d'erreurs dans les logs : `docker-compose logs`

### Tests de connexion

```powershell
# Tester le backend
Invoke-WebRequest -Uri "http://localhost:8004/api/health" -UseBasicParsing

# Tester la base de données
cd simveb-backend-develop
docker-compose exec db psql -U simveb -d simveb -c "SELECT version();"

# Vérifier les tables
docker-compose exec db psql -U simveb -d simveb -c "\dt"
```

---

## 🏗️ Architecture des Composants

### Backend (Laravel)

**Structure** :
```
simveb-backend-develop/
├── app/                    # Code de l'application
│   ├── Http/              # Contrôleurs, middleware
│   ├── Models/            # Modèles Eloquent
│   └── Services/          # Logique métier
├── config/                # Fichiers de configuration
├── database/              # Migrations, seeders
├── ntech-libs/           # Packages personnalisés
│   ├── activity-log-package/
│   ├── metadata-package/
│   ├── notifier-package/
│   ├── required-document-package/
│   └── users-package/
├── routes/               # Définition des routes API
├── docker-compose.yml    # Configuration Docker
└── .env                  # Variables d'environnement
```

**Services Docker** :
- `app` : PHP-FPM 8.2
- `nginx` : Serveur web
- `db` : PostgreSQL 14

### Portal (Nuxt.js)

**Structure** :
```
simveb-portal-design-develop/
├── components/           # Composants Vue réutilisables
├── pages/               # Pages de l'application
├── layouts/             # Layouts Nuxt
├── plugins/             # Plugins Vue/Nuxt
├── middleware/          # Middleware de navigation
├── stores/              # Stores Pinia
├── nuxt.config.ts       # Configuration Nuxt
└── .env                 # Variables d'environnement
```

### Backoffice (Vuero)

**Structure** :
```
simveb-backoffice-develop/
├── src/
│   ├── components/      # Composants Vue
│   ├── pages/          # Pages de l'application
│   ├── layouts/        # Layouts Vuero
│   ├── stores/         # Stores Pinia
│   └── data/           # Données statiques
├── vite.config.ts      # Configuration Vite
└── .env                # Variables d'environnement
```

### Affiliate

**Structure** :
```
simveb-affiliate-develop/
├── src/
│   ├── components/     # Composants Vue
│   ├── views/         # Vues de l'application
│   ├── router/        # Configuration du routeur
│   └── stores/        # Stores Pinia
├── vite.config.ts     # Configuration Vite
└── .env               # Variables d'environnement
```

---

## 🔐 Sécurité et Bonnes Pratiques

### Variables d'environnement sensibles

⚠️ **Ne jamais commiter les fichiers `.env`**

Les fichiers `.env` contiennent des informations sensibles :
- Clés d'API
- Mots de passe de base de données
- Secrets OAuth2
- Clés de chiffrement

### Mots de passe par défaut à changer

En production, changez impérativement :
- `DB_PASSWORD` : Mot de passe PostgreSQL
- `APP_KEY` : Clé de chiffrement Laravel
- Secrets Laravel Passport

### HTTPS en production

En production, utilisez toujours HTTPS :
- Configurez un certificat SSL/TLS
- Utilisez Let's Encrypt pour des certificats gratuits
- Redirigez automatiquement HTTP vers HTTPS

---

## 📦 Build de Production

### Backend

Le backend est déjà configuré pour Docker :

```powershell
cd simveb-backend-develop

# Build de production
docker-compose -f docker-compose.prod.yml build

# Démarrer en production
docker-compose -f docker-compose.prod.yml up -d
```

### Portal

```powershell
cd simveb-portal-design-develop

# Build de production
npm run build

# Démarrer le serveur de production
npm run start

# OU générer des fichiers statiques
npm run generate
```

### Backoffice

```powershell
cd simveb-backoffice-develop

# Build de production
pnpm build

# Prévisualiser le build
pnpm preview

# OU avec Server-Side Rendering (SSR)
pnpm ssr:build
pnpm ssr:serve
```

### Affiliate

```powershell
cd simveb-affiliate-develop

# Build de production
pnpm build

# Prévisualiser le build
pnpm preview
```

---

## 🔄 Mise à Jour du Projet

### Mise à jour du code

```powershell
# Récupérer les dernières modifications
git pull origin main

# Backend
cd simveb-backend-develop
docker-compose exec app composer install
docker-compose exec app php artisan migrate

# Portal
cd ..\simveb-portal-design-develop
npm install

# Backoffice
cd ..\simveb-backoffice-develop
pnpm install

# Affiliate
cd ..\simveb-affiliate-develop
pnpm install
```

### Mise à jour des dépendances

```powershell
# Backend
docker-compose exec app composer update

# Portal
npm update

# Backoffice
pnpm update

# Affiliate
pnpm update
```

---

## 📞 Support et Ressources

### Documentation officielle

- **Laravel** : https://laravel.com/docs
- **Vue.js** : https://vuejs.org/guide/
- **Nuxt.js** : https://nuxt.com/docs
- **Vite** : https://vitejs.dev/
- **Docker** : https://docs.docker.com/

### Outils utiles

- **Postman** : Tester les API REST
- **DBeaver** : Client PostgreSQL GUI
- **Vue DevTools** : Extension navigateur pour déboguer Vue
- **Docker Desktop** : Interface graphique pour Docker

### Commandes utiles

```powershell
# Voir les conteneurs en cours d'exécution
docker ps

# Voir les logs en temps réel
docker-compose logs -f

# Accéder à un conteneur
docker-compose exec app bash

# Nettoyer Docker
docker system prune -a

# Voir les processus Node.js
Get-Process node

# Tuer tous les processus Node.js
Get-Process node | Stop-Process -Force
```

---

## 📈 Performances et Optimisation

### Backend

```powershell
# Mettre en cache les configurations
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Optimiser l'autoloader Composer
docker-compose exec app composer dump-autoload -o
```

### Frontend

```powershell
# Analyser la taille du bundle
npm run build -- --report  # Portal
pnpm build && pnpm analyze  # Backoffice/Affiliate
```

---

## ✅ Checklist de Déploiement

### Avant de commencer

- [ ] Docker Desktop installé et démarré
- [ ] Node.js 18+ installé
- [ ] pnpm installé
- [ ] Git installé et configuré
- [ ] 10 GB d'espace disque disponible
- [ ] Connexion Internet active

### Installation

- [ ] Backend : Conteneurs Docker démarrés
- [ ] Backend : Migrations exécutées
- [ ] Backend : Seeds exécutés
- [ ] Backend : Passport installé
- [ ] Portal : Dépendances installées
- [ ] Portal : Serveur de développement démarré
- [ ] Backoffice : Dépendances installées (pnpm)
- [ ] Backoffice : Serveur de développement démarré
- [ ] Affiliate : Dépendances installées (pnpm)
- [ ] Affiliate : Serveur de développement démarré

### Vérification

- [ ] Backend répond sur http://localhost:8004
- [ ] Portal répond sur http://localhost:3000
- [ ] Backoffice répond sur http://localhost:3001
- [ ] Affiliate répond sur http://localhost:5173
- [ ] Aucune erreur dans les logs
- [ ] Connexion à la base de données OK

---

## 🎯 Conclusion

Vous disposez maintenant d'un environnement de développement SIMVEB complet sur Windows !

**Prochaines étapes** :
1. Consulter la documentation de chaque composant
2. Configurer votre IDE (VS Code recommandé)
3. Installer les extensions recommandées (Vue, PHP, Docker)
4. Commencer à développer !

Pour toute question ou problème, consultez la section **Résolution des Problèmes** ci-dessus.

**Bon développement ! 🚀**
