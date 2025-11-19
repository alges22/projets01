# Guide de Déploiement Windows - SIMVEB

## 🎯 Démarrage Rapide

Ce projet dispose de **scripts automatisés** pour simplifier le déploiement sur Windows.

### Déploiement Automatique (Recommandé)

```powershell
# 1. Autoriser les scripts PowerShell
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force

# 2. Lancer le script de déploiement principal
.\deploy-windows.ps1
```

Un menu interactif vous guidera dans le processus de déploiement.

### Déploiement Manuel

Consultez le **[Guide Complet de Déploiement Windows](DEPLOYMENT_WINDOWS.md)** pour des instructions détaillées.

## 📦 Structure du Projet

```
projets01/
├── DEPLOYMENT_WINDOWS.md           # 📖 Guide complet de déploiement
├── deploy-windows.ps1              # 🚀 Script principal de déploiement
├── README-DEPLOYMENT.md            # 📋 Ce fichier
│
├── scripts/                        # 🔧 Scripts individuels
│   ├── README.md                   # Documentation des scripts
│   ├── deploy-backend.ps1          # Déploiement du backend
│   ├── deploy-portal.ps1           # Déploiement du portal
│   ├── deploy-backoffice.ps1       # Déploiement du backoffice
│   ├── deploy-affiliate.ps1        # Déploiement de l'affiliate
│   └── manage-services.ps1         # Gestion des services
│
├── simveb-backend-develop/         # Backend Laravel
├── simveb-portal-design-develop/   # Portal Nuxt.js
├── simveb-backoffice-develop/      # Backoffice Vuero
└── simveb-affiliate-develop/       # Affiliate
```

## 🛠️ Prérequis

Avant de commencer, installez :

- **Docker Desktop** : https://www.docker.com/products/docker-desktop
- **Node.js 18+** : https://nodejs.org/
- **pnpm** : `npm install -g pnpm`

## 🚀 Options de Déploiement

### Option 1 : Menu Interactif (Le plus simple)

```powershell
.\deploy-windows.ps1
```

### Option 2 : Déploiement Complet en Une Commande

```powershell
.\deploy-windows.ps1 -Component all
```

### Option 3 : Déploiement Composant par Composant

```powershell
# Backend uniquement
.\deploy-windows.ps1 -Component backend

# Portal uniquement
.\deploy-windows.ps1 -Component portal

# Backoffice uniquement
.\deploy-windows.ps1 -Component backoffice

# Affiliate uniquement
.\deploy-windows.ps1 -Component affiliate
```

### Option 4 : Scripts Individuels

```powershell
cd scripts

# Déployer chaque composant
.\deploy-backend.ps1
.\deploy-portal.ps1 -Start
.\deploy-backoffice.ps1 -Port 3001 -Start
.\deploy-affiliate.ps1 -Start
```

## 📊 Gestion des Services

### Menu de Gestion

```powershell
cd scripts
.\manage-services.ps1
```

### Commandes Rapides

```powershell
# Voir le statut
.\manage-services.ps1 -Action status

# Arrêter tout
.\manage-services.ps1 -Action stop -Service all

# Voir les logs du backend
.\manage-services.ps1 -Action logs -Service backend
```

## 🌐 URLs d'Accès

| Service | URL | Port |
|---------|-----|------|
| **Backend API** | http://localhost:8004 | 8004 |
| **Portal** | http://localhost:3000 | 3000 |
| **Backoffice** | http://localhost:3001 | 3001 |
| **Affiliate** | http://localhost:5173 | 5173 |

## 📚 Documentation

- **[DEPLOYMENT_WINDOWS.md](DEPLOYMENT_WINDOWS.md)** - Guide complet de déploiement
- **[scripts/README.md](scripts/README.md)** - Documentation des scripts PowerShell

## ⚠️ Notes Importantes

### Conflit de Ports

Le Portal et le Backoffice utilisent par défaut le port **3000**. Les scripts configurent automatiquement le Backoffice sur le port **3001** pour éviter les conflits.

### Gestionnaires de Paquets

- **Portal** : Utilise `npm` ou `yarn`
- **Backoffice** : Nécessite `pnpm` (obligatoire)
- **Affiliate** : Nécessite `pnpm` (obligatoire)

### Ordre de Démarrage

1. **Backend d'abord** (automatique avec Docker)
2. **Puis les frontends** (dans des terminaux séparés)

## 🔧 Résolution Rapide des Problèmes

### Docker ne démarre pas

```powershell
# Redémarrer Docker Desktop
# Attendre que l'icône soit verte
```

### pnpm non trouvé

```powershell
npm install -g pnpm
# Redémarrer le terminal
```

### Port déjà utilisé

```powershell
cd scripts
.\manage-services.ps1 -Action stop -Service all
```

### Erreurs de script PowerShell

```powershell
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force
```

## 🎯 Workflow Recommandé

### Première Utilisation

1. Exécuter `.\deploy-windows.ps1`
2. Choisir l'option **[1] Déploiement complet**
3. Attendre la fin de la configuration
4. Démarrer les frontends nécessaires

### Développement Quotidien

```powershell
# Terminal 1 - Backend (toujours démarré en premier)
cd simveb-backend-develop
docker-compose up -d

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

### Fin de Journée

```powershell
cd scripts
.\manage-services.ps1 -Action stop -Service all
```

## 💡 Astuces

### Voir les Logs en Temps Réel

```powershell
# Backend
cd simveb-backend-develop
docker-compose logs -f app

# Ou via le script
cd scripts
.\manage-services.ps1 -Action logs -Service backend
```

### Nettoyer Docker

```powershell
cd scripts
.\manage-services.ps1
# Choisir l'option [6] Nettoyer les conteneurs Docker
```

### Réinitialiser un Composant

```powershell
# Backend
cd simveb-backend-develop
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed

# Frontend
cd simveb-portal-design-develop
Remove-Item -Recurse -Force node_modules
npm install
```

## 🆘 Besoin d'Aide ?

1. **Consultez** le [Guide Complet](DEPLOYMENT_WINDOWS.md)
2. **Vérifiez** la [Documentation des Scripts](scripts/README.md)
3. **Testez** le statut : `.\manage-services.ps1 -Action status`
4. **Consultez** les logs : `.\manage-services.ps1 -Action logs`

## 🎉 C'est Parti !

Vous êtes prêt à déployer SIMVEB sur Windows. Bonne chance ! 🚀

---

**Note** : Ce guide a été créé pour simplifier le déploiement local de SIMVEB sur Windows. Pour un déploiement en production, consultez la documentation officielle de chaque composant.
