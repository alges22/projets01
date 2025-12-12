# Guide de Déploiement SIMVEB sur Windows SANS Docker

## 📋 Vue d'ensemble

Ce guide détaille le processus complet de déploiement local du projet SIMVEB sur Windows **sans utiliser Docker**.

Tous les services seront installés et exécutés nativement sur Windows.

---

## 🔧 Architecture Sans Docker

```
Windows localhost
├── PHP 8.2+ (avec php.exe)
├── Composer (gestionnaire de dépendances PHP)
├── PostgreSQL 14+ (base de données)
├── Redis (cache)
├── Node.js 18+ (pour les frontends)
├── pnpm (gestionnaire de paquets)
└── Git for Windows
```

---

## 📦 PHASE 1 : Installation des Prérequis

### 1.1 PHP 8.2+ (CRITIQUE)

#### Téléchargement

1. **Aller sur** : https://windows.php.net/download/
2. **Choisir** : PHP 8.2.x (Thread Safe) - ZIP
3. **Télécharger** : `php-8.2.x-Win32-vs16-x64.zip`

#### Installation

```powershell
# Créer un dossier pour PHP
New-Item -ItemType Directory -Path "C:\php" -Force

# Extraire le ZIP téléchargé dans C:\php
# (Faire un clic droit > Extraire tout > C:\php)

# Ajouter PHP au PATH
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\php", "User")

# Redémarrer PowerShell, puis vérifier
php -v
# Doit afficher : PHP 8.2.x
```

#### Configuration de php.ini

```powershell
# Copier le fichier de config
Copy-Item C:\php\php.ini-development C:\php\php.ini

# Éditer php.ini
notepad C:\php\php.ini
```

**Décommenter (enlever le `;` devant) ces lignes dans php.ini :**

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_pgsql
extension=pgsql
extension=zip
extension=redis

; Augmenter les limites
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
```

**Vérifier les extensions :**

```powershell
php -m
# Doit afficher : pgsql, pdo_pgsql, redis, curl, mbstring, etc.
```

---

### 1.2 Composer (Gestionnaire de dépendances PHP)

#### Installation

```powershell
# Télécharger l'installeur
Invoke-WebRequest -Uri "https://getcomposer.org/Composer-Setup.exe" -OutFile "$env:TEMP\Composer-Setup.exe"

# Exécuter l'installeur
Start-Process -FilePath "$env:TEMP\Composer-Setup.exe" -Wait

# Redémarrer PowerShell, puis vérifier
composer --version
# Doit afficher : Composer version 2.x.x
```

**Si composer n'est pas reconnu :**

```powershell
# Ajouter au PATH
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\ProgramData\ComposerSetup\bin", "User")

# Redémarrer PowerShell
```

---

### 1.3 PostgreSQL 14+ (Base de données)

#### Installation

1. **Télécharger** : https://www.postgresql.org/download/windows/
2. **Choisir** : PostgreSQL 14.x (ou version supérieure)
3. **Exécuter** l'installeur `postgresql-14.x-windows-x64.exe`

#### Configuration pendant l'installation

- **Port** : 5432 (par défaut)
- **Superuser password** : `password` (ou votre choix)
- **Locale** : French, France (ou English, United States)

#### Créer la base de données SIMVEB

```powershell
# Ouvrir SQL Shell (psql) depuis le menu Windows
# Ou via PowerShell :

# Se connecter à PostgreSQL
& "C:\Program Files\PostgreSQL\14\bin\psql.exe" -U postgres

# Dans psql, exécuter :
```

```sql
-- Créer l'utilisateur
CREATE USER simveb WITH PASSWORD 'password';

-- Créer la base de données
CREATE DATABASE simveb OWNER simveb;

-- Donner tous les privilèges
GRANT ALL PRIVILEGES ON DATABASE simveb TO simveb;

-- Quitter psql
\q
```

#### Vérifier la connexion

```powershell
& "C:\Program Files\PostgreSQL\14\bin\psql.exe" -U simveb -d simveb
# Entrer le mot de passe : password
# Si connexion OK, taper \q pour quitter
```

---

### 1.4 Redis (Cache)

#### Installation avec Memurai (Redis pour Windows)

**Option A : Memurai (Recommandé - Compatible Redis)**

1. **Télécharger** : https://www.memurai.com/get-memurai
2. **Installer** : `Memurai-Setup.exe`
3. **Démarrer** : Le service démarre automatiquement

**Option B : Redis Windows Fork**

1. **Télécharger** : https://github.com/tporadowski/redis/releases
2. **Extraire** dans `C:\Redis`
3. **Installer le service** :

```powershell
cd C:\Redis
.\redis-server.exe --service-install redis.windows.conf
.\redis-server.exe --service-start
```

#### Vérifier Redis

```powershell
# Tester la connexion
redis-cli ping
# Doit retourner : PONG

# Ou si redis-cli n'est pas dans le PATH :
& "C:\Program Files\Memurai\memurai-cli.exe" ping
```

---

### 1.5 Node.js 18+ LTS

#### Installation

1. **Télécharger** : https://nodejs.org/
2. **Choisir** : LTS (Long Term Support) - Version 18.x ou 20.x
3. **Installer** : `node-vXX.X.X-x64.msi`
4. **Cocher** : "Automatically install the necessary tools" (optionnel)

#### Vérification

```powershell
# Vérifier Node.js
node --version
# Doit afficher : v18.x.x ou v20.x.x

# Vérifier npm
npm --version
# Doit afficher : 9.x.x ou 10.x.x
```

---

### 1.6 pnpm (Gestionnaire de paquets)

```powershell
# Installer pnpm globalement
npm install -g pnpm

# Vérifier
pnpm --version
# Doit afficher : 8.x.x ou 9.x.x
```

---

### 1.7 Git for Windows

1. **Télécharger** : https://git-scm.com/download/win
2. **Installer** : `Git-2.XX.X-64-bit.exe`
3. **Configurer** :

```powershell
git config --global core.autocrlf input
git config --global user.name "Votre Nom"
git config --global user.email "votre.email@example.com"
```

---

## 📂 PHASE 2 : Cloner et Préparer le Projet

### 2.1 Cloner le projet (si pas déjà fait)

```powershell
# Naviguer vers votre dossier de travail
cd C:\Users\VotreNom\Documents

# Cloner le projet
git clone <URL_DU_REPO> projets01
cd projets01

# Vérifier la structure
dir
# Doit afficher : simveb-backend-develop, simveb-portal-design-develop, etc.
```

---

## 🚀 PHASE 3 : Configuration et Déploiement du Backend

### 3.1 Naviguer vers le backend

```powershell
cd simveb-backend-develop
```

### 3.2 Créer le fichier .env

```powershell
# Copier l'exemple
Copy-Item .env.example .env

# Éditer
notepad .env
```

### 3.3 Configuration du fichier .env

**Modifier ces valeurs dans `.env` :**

```env
APP_NAME=SimVeb
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=simveb
DB_USERNAME=simveb
DB_PASSWORD=password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# URLs des frontends
PORTAL_URL=http://localhost:3000
ADMIN_URL=http://localhost:3001
AFFILIATE_URL=http://localhost:5173

# Mail (configuration de base pour tests)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@simveb.bj"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.4 Installer les dépendances PHP

```powershell
# Installer les packages Composer
composer install

# Si erreur de mémoire :
php -d memory_limit=-1 C:\ProgramData\ComposerSetup\bin\composer.phar install
```

**⏱️ Temps estimé : 3-5 minutes**

### 3.5 Générer la clé d'application

```powershell
php artisan key:generate
# Doit afficher : Application key set successfully.
```

### 3.6 Créer le lien de stockage

```powershell
php artisan storage:link
# Doit afficher : The [public/storage] link has been connected to [storage/app/public].
```

### 3.7 Exécuter les migrations de base de données

```powershell
# Créer les tables
php artisan migrate

# Si demande de confirmation, taper : yes
```

**⏱️ Temps estimé : 1-2 minutes**

### 3.8 Peupler la base de données (Seeders)

```powershell
# Insérer les données de base
php artisan db:seed

# Ou migrer + seed en une seule commande :
# php artisan migrate:fresh --seed
```

**⏱️ Temps estimé : 2-3 minutes**

### 3.9 Installer Laravel Passport (OAuth2)

```powershell
# Installer Passport
php artisan passport:install

# Noter les clés générées (Client ID et Secret)
```

**Résultat attendu :**

```
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: XXXXXXXXXXXXXXXXXXXX
Password grant client created successfully.
Client ID: 2
Client secret: XXXXXXXXXXXXXXXXXXXX
```

### 3.10 Optimiser le cache (optionnel en dev)

```powershell
# Mettre en cache les configs
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Si vous modifiez des configs plus tard, nettoyer avec :
# php artisan config:clear
# php artisan route:clear
# php artisan view:clear
```

### 3.11 Démarrer le serveur Laravel

```powershell
# Démarrer sur le port 8000 (ou autre port disponible)
php artisan serve --host=0.0.0.0 --port=8000

# Ou uniquement localhost :
php artisan serve
```

**✅ Backend démarré sur : http://localhost:8000**

### 3.12 Vérifier que le backend fonctionne

**Ouvrir un NOUVEAU terminal PowerShell** et tester :

```powershell
# Tester l'API
Invoke-WebRequest -Uri "http://localhost:8000/api/health" -UseBasicParsing

# Ou ouvrir dans le navigateur :
# http://localhost:8000
```

**⚠️ Laisser ce terminal ouvert (le serveur PHP tourne ici)**

---

## 🌐 PHASE 4 : Configuration et Déploiement du Portal (Nuxt.js)

### 4.1 Ouvrir un NOUVEAU terminal PowerShell

```powershell
# Naviguer vers le portal
cd C:\Users\VotreNom\Documents\projets01\simveb-portal-design-develop
```

### 4.2 Créer le fichier .env

```powershell
# Copier l'exemple
Copy-Item .env.example .env

# Éditer
notepad .env
```

### 4.3 Configuration du fichier .env

```env
VITE_API_URL=http://localhost:8000/api
VITE_PORTAL_URL=http://localhost:3000
VITE_ADMIN_URL=http://localhost:3001
VITE_AFFILIATE_URL=http://localhost:5173
```

### 4.4 Installer les dépendances Node.js

```powershell
# Installer avec npm
npm install
```

**⏱️ Temps estimé : 3-5 minutes**

### 4.5 Démarrer le serveur de développement

```powershell
# Démarrer sur le port 3000
npm run dev
```

**✅ Portal démarré sur : http://localhost:3000**

**⚠️ Laisser ce terminal ouvert (le serveur Nuxt tourne ici)**

---

## 🏢 PHASE 5 : Configuration et Déploiement du Backoffice (Vuero)

### 5.1 Ouvrir un NOUVEAU terminal PowerShell

```powershell
# Naviguer vers le backoffice
cd C:\Users\VotreNom\Documents\projets01\simveb-backoffice-develop
```

### 5.2 Créer le fichier .env

```powershell
# Copier l'exemple
Copy-Item .env.example .env

# Éditer
notepad .env
```

### 5.3 Configuration du fichier .env

```env
VITE_API_URL=http://localhost:8000/api
VITE_ADMIN_URL=http://localhost:3001
```

### 5.4 Installer les dépendances avec pnpm

```powershell
# ⚠️ IMPORTANT : Utiliser pnpm (pas npm)
pnpm install
```

**⏱️ Temps estimé : 3-5 minutes**

### 5.5 Démarrer le serveur de développement

```powershell
# Démarrer sur le port 3001 (pour éviter conflit avec Portal)
pnpm dev -- --port 3001
```

**✅ Backoffice démarré sur : http://localhost:3001**

**⚠️ Laisser ce terminal ouvert (le serveur Vite tourne ici)**

---

## 🔗 PHASE 6 : Configuration et Déploiement de l'Affiliate

### 6.1 Ouvrir un NOUVEAU terminal PowerShell

```powershell
# Naviguer vers affiliate
cd C:\Users\VotreNom\Documents\projets01\simveb-affiliate-develop
```

### 6.2 Créer le fichier .env

```powershell
# Copier l'exemple
Copy-Item .env.example .env

# Éditer
notepad .env
```

### 6.3 Configuration du fichier .env

```env
VITE_API_URL=http://localhost:8000/api
VITE_ADMIN_URL=http://localhost:3001
VITE_AFFILIATE_URL=http://localhost:5173
```

### 6.4 Installer les dépendances avec pnpm

```powershell
# Installer avec pnpm
pnpm install
```

**⏱️ Temps estimé : 2-3 minutes**

### 6.5 Démarrer le serveur de développement

```powershell
# Démarrer sur le port 5173 (port par défaut de Vite)
pnpm dev
```

**✅ Affiliate démarré sur : http://localhost:5173**

**⚠️ Laisser ce terminal ouvert (le serveur Vite tourne ici)**

---

## ✅ PHASE 7 : Vérification Complète

### 7.1 Résumé des terminaux ouverts

Vous devez avoir **4 terminaux PowerShell ouverts** :

| Terminal | Service | Commande | Port | URL |
|----------|---------|----------|------|-----|
| **1** | Backend Laravel | `php artisan serve` | 8000 | http://localhost:8000 |
| **2** | Portal Nuxt.js | `npm run dev` | 3000 | http://localhost:3000 |
| **3** | Backoffice Vuero | `pnpm dev -- --port 3001` | 3001 | http://localhost:3001 |
| **4** | Affiliate | `pnpm dev` | 5173 | http://localhost:5173 |

### 7.2 Tests de connexion

Ouvrir un **5ème terminal** pour tester :

```powershell
# Test Backend
Invoke-WebRequest -Uri "http://localhost:8000/api/health" -UseBasicParsing

# Test Portal
Invoke-WebRequest -Uri "http://localhost:3000" -UseBasicParsing

# Test Backoffice
Invoke-WebRequest -Uri "http://localhost:3001" -UseBasicParsing

# Test Affiliate
Invoke-WebRequest -Uri "http://localhost:5173" -UseBasicParsing
```

### 7.3 Tester la base de données

```powershell
# Se connecter à PostgreSQL
& "C:\Program Files\PostgreSQL\14\bin\psql.exe" -U simveb -d simveb

# Dans psql, lister les tables :
\dt

# Compter les utilisateurs :
SELECT COUNT(*) FROM users;

# Quitter :
\q
```

### 7.4 Tester Redis

```powershell
# Tester Redis
redis-cli ping
# Doit retourner : PONG

# Voir les clés :
redis-cli keys *

# Ou avec Memurai :
& "C:\Program Files\Memurai\memurai-cli.exe" ping
```

---

## 🔄 Workflow de Développement Quotidien

### Démarrage (chaque jour)

#### 1. Vérifier que PostgreSQL est démarré

```powershell
# Vérifier le service PostgreSQL
Get-Service -Name postgresql*

# Si arrêté, démarrer :
Start-Service -Name postgresql-x64-14
```

#### 2. Vérifier que Redis/Memurai est démarré

```powershell
# Vérifier le service Memurai
Get-Service -Name Memurai

# Si arrêté, démarrer :
Start-Service -Name Memurai

# Ou pour Redis :
Get-Service -Name Redis
Start-Service -Name Redis
```

#### 3. Démarrer les 4 terminaux

**Terminal 1 - Backend :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-backend-develop
php artisan serve
```

**Terminal 2 - Portal :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-portal-design-develop
npm run dev
```

**Terminal 3 - Backoffice :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-backoffice-develop
pnpm dev -- --port 3001
```

**Terminal 4 - Affiliate :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-affiliate-develop
pnpm dev
```

### Arrêt (fin de journée)

Dans chaque terminal, appuyer sur **Ctrl + C** pour arrêter le serveur.

---

## 🗂️ Import du Dump SQL (Optionnel)

Si vous avez un fichier SQL à importer (`simvebbase (1).sql`) :

```powershell
# Naviguer vers le dossier contenant le fichier SQL
cd C:\Users\VotreNom\Documents\projets01

# Importer dans PostgreSQL
& "C:\Program Files\PostgreSQL\14\bin\psql.exe" -U simveb -d simveb -f "simvebbase (1).sql"

# Entrer le mot de passe : password
```

**⏱️ Temps estimé : 2-5 minutes selon la taille du dump**

---

## ⚙️ Configuration des Workers Queue (Optionnel)

Si votre application utilise des jobs en arrière-plan :

### Démarrer le worker Laravel

Ouvrir un **nouveau terminal** :

```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-backend-develop

# Démarrer le worker
php artisan queue:work redis --tries=3 --timeout=90
```

**⚠️ Laisser ce terminal ouvert pendant le développement**

---

## 🔧 Configuration avancée de PHP

### Installer l'extension Redis pour PHP

Si l'extension Redis n'est pas disponible :

1. **Télécharger** : https://pecl.php.net/package/redis
2. **Ou utiliser** : https://windows.php.net/downloads/pecl/releases/redis/
3. **Extraire** `php_redis.dll` dans `C:\php\ext\`
4. **Activer** dans `php.ini` :

```ini
extension=php_redis.dll
```

5. **Vérifier** :

```powershell
php -m | findstr redis
# Doit afficher : redis
```

---

## ⚠️ Résolution des Problèmes

### 1. Erreur : "Class 'Redis' not found"

**Cause :** Extension Redis non installée pour PHP

**Solution :**
```powershell
# Vérifier les extensions chargées
php -m

# Si redis n'apparaît pas, éditer php.ini
notepad C:\php\php.ini

# Ajouter ou décommenter :
extension=redis

# Redémarrer le serveur Laravel
```

### 2. Erreur : "SQLSTATE[08006] [7] could not connect to server"

**Cause :** PostgreSQL n'est pas démarré

**Solution :**
```powershell
# Démarrer PostgreSQL
Start-Service -Name postgresql-x64-14

# Vérifier qu'il tourne
Get-Service -Name postgresql*
```

### 3. Erreur : "Connection refused [tcp://127.0.0.1:6379]"

**Cause :** Redis/Memurai n'est pas démarré

**Solution :**
```powershell
# Démarrer Memurai
Start-Service -Name Memurai

# Ou Redis :
Start-Service -Name Redis

# Vérifier
redis-cli ping
```

### 4. Erreur : "php artisan not found"

**Cause :** Vous n'êtes pas dans le bon dossier

**Solution :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-backend-develop
php artisan serve
```

### 5. Erreur : "Port 8000 already in use"

**Cause :** Un autre processus utilise le port

**Solution :**
```powershell
# Trouver le processus
netstat -ano | findstr :8000

# Tuer le processus (remplacer PID)
taskkill /PID <PID> /F

# Ou utiliser un autre port
php artisan serve --port=8001
```

### 6. Erreur : "Composer: Maximum execution time exceeded"

**Solution :**
```powershell
# Augmenter la limite dans php.ini
notepad C:\php\php.ini
# Modifier : max_execution_time = 300

# Ou installer avec limite désactivée
php -d memory_limit=-1 -d max_execution_time=0 C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### 7. Erreur : "npm ERR! ENOENT: no such file or directory"

**Solution :**
```powershell
# Supprimer node_modules et réinstaller
Remove-Item -Recurse -Force node_modules
Remove-Item -Force package-lock.json
npm install
```

### 8. Frontend affiche "Network Error"

**Cause :** Backend non démarré ou URL incorrecte dans .env

**Solution :**
```powershell
# Vérifier que le backend tourne
Invoke-WebRequest -Uri "http://localhost:8000/api/health"

# Vérifier les .env des frontends
# VITE_API_URL doit être : http://localhost:8000/api
```

### 9. Erreur CORS (Access-Control-Allow-Origin)

**Cause :** Configuration CORS du backend

**Solution :**
```powershell
# Éditer config/cors.php dans le backend
cd simveb-backend-develop
notepad config/cors.php

# Vérifier que 'paths' inclut 'api/*'
# Vérifier que 'allowed_origins' inclut les URLs des frontends

# Nettoyer le cache
php artisan config:clear
php artisan cache:clear
```

### 10. PostgreSQL : "FATAL: password authentication failed"

**Solution :**
```powershell
# Vérifier les credentials dans .env du backend
notepad .env

# Doivent correspondre à ce qui a été configuré :
# DB_USERNAME=simveb
# DB_PASSWORD=password

# Ou réinitialiser le mot de passe dans PostgreSQL
& "C:\Program Files\PostgreSQL\14\bin\psql.exe" -U postgres

# Dans psql :
ALTER USER simveb WITH PASSWORD 'password';
\q
```

---

## 📋 Checklist Complète d'Installation

### Prérequis installés

- [ ] PHP 8.2+ installé et dans le PATH
- [ ] Extensions PHP activées (pgsql, redis, curl, mbstring, etc.)
- [ ] Composer installé
- [ ] PostgreSQL 14+ installé et démarré
- [ ] Base de données `simveb` créée
- [ ] Redis ou Memurai installé et démarré
- [ ] Node.js 18+ installé
- [ ] pnpm installé globalement
- [ ] Git for Windows installé

### Backend configuré

- [ ] Fichier .env créé et configuré
- [ ] Dépendances Composer installées
- [ ] Clé d'application générée
- [ ] Lien de stockage créé
- [ ] Migrations exécutées
- [ ] Seeders exécutés
- [ ] Laravel Passport installé
- [ ] Serveur Laravel démarré (port 8000)

### Frontends configurés

- [ ] Portal : .env créé, dépendances installées, serveur démarré (port 3000)
- [ ] Backoffice : .env créé, dépendances pnpm installées, serveur démarré (port 3001)
- [ ] Affiliate : .env créé, dépendances pnpm installées, serveur démarré (port 5173)

### Vérifications finales

- [ ] Backend répond : http://localhost:8000
- [ ] Portal répond : http://localhost:3000
- [ ] Backoffice répond : http://localhost:3001
- [ ] Affiliate répond : http://localhost:5173
- [ ] Connexion PostgreSQL OK
- [ ] Connexion Redis OK
- [ ] Aucune erreur dans les terminaux

---

## 🎯 Commandes Récapitulatives

### Démarrage Complet (Copy-Paste)

**Terminal 1 - Backend :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-backend-develop
php artisan serve
```

**Terminal 2 - Portal :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-portal-design-develop
npm run dev
```

**Terminal 3 - Backoffice :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-backoffice-develop
pnpm dev -- --port 3001
```

**Terminal 4 - Affiliate :**
```powershell
cd C:\Users\VotreNom\Documents\projets01\simveb-affiliate-develop
pnpm dev
```

### Vérification des Services

```powershell
# PostgreSQL
Get-Service -Name postgresql*

# Redis/Memurai
Get-Service -Name Memurai

# Tester les URLs
Invoke-WebRequest -Uri "http://localhost:8000/api/health"
Invoke-WebRequest -Uri "http://localhost:3000"
Invoke-WebRequest -Uri "http://localhost:3001"
Invoke-WebRequest -Uri "http://localhost:5173"
```

### Nettoyage et Réinitialisation

```powershell
# Backend
cd simveb-backend-develop
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate:fresh --seed

# Frontend (dans chaque dossier frontend)
Remove-Item -Recurse -Force node_modules
Remove-Item -Force package-lock.json
npm install  # ou pnpm install
```

---

## 📊 Temps Total d'Installation

| Phase | Temps Estimé |
|-------|--------------|
| Installation des prérequis | 30-45 min |
| Installation Backend | 10-15 min |
| Installation Portal | 5-10 min |
| Installation Backoffice | 5-10 min |
| Installation Affiliate | 5-10 min |
| Tests et vérifications | 5-10 min |
| **TOTAL** | **60-100 min** |

---

## 🎓 Ressources Complémentaires

### Documentation officielle

- **PHP** : https://www.php.net/manual/fr/
- **Composer** : https://getcomposer.org/doc/
- **Laravel** : https://laravel.com/docs/10.x
- **PostgreSQL** : https://www.postgresql.org/docs/
- **Node.js** : https://nodejs.org/docs/
- **Nuxt.js** : https://nuxt.com/docs
- **Vue.js** : https://vuejs.org/guide/

### Outils utiles

- **HeidiSQL** : Client GUI pour PostgreSQL
- **Redis Desktop Manager** : Client GUI pour Redis
- **Postman** : Tester les API REST
- **VS Code** : Éditeur de code recommandé

### Extensions VS Code recommandées

- PHP Intelephense
- Laravel Extension Pack
- Vue Language Features (Volar)
- PostgreSQL
- Redis

---

## ✅ Vous avez terminé !

Félicitations ! 🎉 Vous avez maintenant un environnement de développement SIMVEB complet sur Windows **sans Docker**.

**Prochaines étapes :**

1. Explorer l'application
2. Lire la documentation métier
3. Commencer le développement

**Bon développement ! 🚀**

---

**Version du document :** 1.0
**Date de création :** 2025-12-12
**Auteur :** Équipe Technique SIMVEB
