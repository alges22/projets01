#Requires -Version 5.1

<#
.SYNOPSIS
    Script de déploiement automatisé SIMVEB pour Windows

.DESCRIPTION
    Ce script automatise le déploiement local du projet SIMVEB sur Windows.
    Il vérifie les prérequis, configure les environnements et démarre les services.

.PARAMETER Component
    Composant à déployer: 'all', 'backend', 'portal', 'backoffice', 'affiliate'

.PARAMETER SkipChecks
    Ignorer la vérification des prérequis

.EXAMPLE
    .\deploy-windows.ps1
    Affiche le menu interactif

.EXAMPLE
    .\deploy-windows.ps1 -Component backend
    Déploie uniquement le backend

.EXAMPLE
    .\deploy-windows.ps1 -Component all -SkipChecks
    Déploie tout en ignorant les vérifications
#>

param(
    [Parameter(Mandatory=$false)]
    [ValidateSet('all', 'backend', 'portal', 'backoffice', 'affiliate', 'menu')]
    [string]$Component = 'menu',

    [Parameter(Mandatory=$false)]
    [switch]$SkipChecks
)

# Couleurs pour l'affichage
$Script:Colors = @{
    Success = 'Green'
    Error = 'Red'
    Warning = 'Yellow'
    Info = 'Cyan'
    Header = 'Magenta'
}

# Configuration des composants
$Script:Config = @{
    Backend = @{
        Name = 'Backend API (Laravel)'
        Path = 'simveb-backend-develop'
        Port = 8004
        RequiresPnpm = $false
        UsesDocker = $true
    }
    Portal = @{
        Name = 'Portal (Nuxt.js)'
        Path = 'simveb-portal-design-develop'
        Port = 3000
        RequiresPnpm = $false
        UsesDocker = $false
    }
    Backoffice = @{
        Name = 'Backoffice (Vuero)'
        Path = 'simveb-backoffice-develop'
        Port = 3000
        RequiresPnpm = $true
        UsesDocker = $false
    }
    Affiliate = @{
        Name = 'Affiliate'
        Path = 'simveb-affiliate-develop'
        Port = 5173
        RequiresPnpm = $true
        UsesDocker = $false
    }
}

# Fonction pour afficher un message formaté
function Write-Message {
    param(
        [string]$Message,
        [string]$Type = 'Info',
        [switch]$NoNewLine
    )

    $color = $Script:Colors[$Type]
    $prefix = switch ($Type) {
        'Success' { '[✓]' }
        'Error'   { '[✗]' }
        'Warning' { '[!]' }
        'Info'    { '[i]' }
        'Header'  { '═══' }
        default   { '   ' }
    }

    if ($NoNewLine) {
        Write-Host "$prefix $Message" -ForegroundColor $color -NoNewline
    } else {
        Write-Host "$prefix $Message" -ForegroundColor $color
    }
}

# Fonction pour afficher le header
function Show-Header {
    Clear-Host
    Write-Host ""
    Write-Host "╔════════════════════════════════════════════════╗" -ForegroundColor $Script:Colors.Header
    Write-Host "║                                                ║" -ForegroundColor $Script:Colors.Header
    Write-Host "║     SIMVEB - Déploiement Windows              ║" -ForegroundColor $Script:Colors.Header
    Write-Host "║     Système Multi-Composants                   ║" -ForegroundColor $Script:Colors.Header
    Write-Host "║                                                ║" -ForegroundColor $Script:Colors.Header
    Write-Host "╚════════════════════════════════════════════════╝" -ForegroundColor $Script:Colors.Header
    Write-Host ""
}

# Fonction pour vérifier si une commande existe
function Test-Command {
    param([string]$Command)

    $null = Get-Command $Command -ErrorAction SilentlyContinue
    return $?
}

# Fonction pour vérifier si un port est utilisé
function Test-Port {
    param([int]$Port)

    $connection = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
    return $null -ne $connection
}

# Fonction pour vérifier les prérequis
function Test-Prerequisites {
    Write-Message "Vérification des prérequis..." -Type Header
    Write-Host ""

    $allOk = $true

    # Vérifier Node.js
    Write-Message "Node.js: " -Type Info -NoNewLine
    if (Test-Command "node") {
        $nodeVersion = node --version
        Write-Host $nodeVersion -ForegroundColor $Script:Colors.Success
    } else {
        Write-Host "NON INSTALLÉ" -ForegroundColor $Script:Colors.Error
        Write-Message "Téléchargez Node.js depuis: https://nodejs.org/" -Type Warning
        $allOk = $false
    }

    # Vérifier npm
    Write-Message "npm: " -Type Info -NoNewLine
    if (Test-Command "npm") {
        $npmVersion = npm --version
        Write-Host "v$npmVersion" -ForegroundColor $Script:Colors.Success
    } else {
        Write-Host "NON INSTALLÉ" -ForegroundColor $Script:Colors.Error
        $allOk = $false
    }

    # Vérifier pnpm
    Write-Message "pnpm: " -Type Info -NoNewLine
    if (Test-Command "pnpm") {
        $pnpmVersion = pnpm --version
        Write-Host "v$pnpmVersion" -ForegroundColor $Script:Colors.Success
    } else {
        Write-Host "NON INSTALLÉ" -ForegroundColor $Script:Colors.Warning
        Write-Message "Installez avec: npm install -g pnpm" -Type Warning
    }

    # Vérifier Docker
    Write-Message "Docker: " -Type Info -NoNewLine
    if (Test-Command "docker") {
        $dockerVersion = docker --version
        Write-Host $dockerVersion -ForegroundColor $Script:Colors.Success

        # Vérifier si Docker est en cours d'exécution
        try {
            $null = docker ps 2>$null
            Write-Message "Docker est en cours d'exécution" -Type Success
        } catch {
            Write-Message "Docker est installé mais pas démarré" -Type Warning
            Write-Message "Démarrez Docker Desktop" -Type Warning
            $allOk = $false
        }
    } else {
        Write-Host "NON INSTALLÉ" -ForegroundColor $Script:Colors.Error
        Write-Message "Téléchargez Docker Desktop depuis: https://www.docker.com/products/docker-desktop" -Type Warning
        $allOk = $false
    }

    # Vérifier Git
    Write-Message "Git: " -Type Info -NoNewLine
    if (Test-Command "git") {
        $gitVersion = git --version
        Write-Host $gitVersion -ForegroundColor $Script:Colors.Success
    } else {
        Write-Host "NON INSTALLÉ" -ForegroundColor $Script:Colors.Warning
        Write-Message "Téléchargez depuis: https://git-scm.com/download/win" -Type Warning
    }

    Write-Host ""

    if ($allOk) {
        Write-Message "Tous les prérequis sont satisfaits!" -Type Success
        return $true
    } else {
        Write-Message "Certains prérequis sont manquants. Veuillez les installer avant de continuer." -Type Error
        return $false
    }
}

# Fonction pour créer le fichier .env s'il n'existe pas
function Initialize-Environment {
    param(
        [string]$ComponentPath,
        [hashtable]$EnvVars
    )

    $envPath = Join-Path $ComponentPath ".env"
    $envExamplePath = Join-Path $ComponentPath ".env.example"

    if (-not (Test-Path $envPath)) {
        if (Test-Path $envExamplePath) {
            Write-Message "Création du fichier .env..." -Type Info
            Copy-Item $envExamplePath $envPath

            # Remplacer les variables si fournies
            if ($EnvVars) {
                $content = Get-Content $envPath
                foreach ($key in $EnvVars.Keys) {
                    $content = $content -replace "^$key=.*", "$key=$($EnvVars[$key])"
                }
                $content | Set-Content $envPath
            }

            Write-Message "Fichier .env créé" -Type Success
        } else {
            Write-Message "Fichier .env.example introuvable" -Type Warning
        }
    } else {
        Write-Message "Fichier .env existe déjà" -Type Info
    }
}

# Fonction pour déployer le backend
function Deploy-Backend {
    Write-Message "Déploiement du Backend API (Laravel)..." -Type Header
    Write-Host ""

    $backendPath = $Script:Config.Backend.Path

    if (-not (Test-Path $backendPath)) {
        Write-Message "Dossier backend introuvable: $backendPath" -Type Error
        return $false
    }

    Push-Location $backendPath

    try {
        # Créer le fichier .env
        Initialize-Environment -ComponentPath "." -EnvVars @{
            'DB_HOST' = 'db'
            'DB_PORT' = '5432'
            'DB_DATABASE' = 'simveb'
            'DB_USERNAME' = 'simveb'
            'DB_PASSWORD' = 'password'
        }

        # Démarrer Docker Compose
        Write-Message "Démarrage des conteneurs Docker..." -Type Info
        docker-compose up -d

        if ($LASTEXITCODE -ne 0) {
            Write-Message "Erreur lors du démarrage de Docker" -Type Error
            return $false
        }

        Write-Message "Attente du démarrage des conteneurs (10 secondes)..." -Type Info
        Start-Sleep -Seconds 10

        # Installer les dépendances
        Write-Message "Installation des dépendances Composer..." -Type Info
        docker-compose exec -T app composer install --no-interaction

        # Générer la clé d'application
        Write-Message "Génération de la clé d'application..." -Type Info
        docker-compose exec -T app php artisan key:generate --force

        # Créer le lien symbolique de stockage
        Write-Message "Création du lien symbolique de stockage..." -Type Info
        docker-compose exec -T app php artisan storage:link

        # Migrations
        Write-Message "Exécution des migrations..." -Type Info
        docker-compose exec -T app php artisan migrate --force

        # Seeds
        Write-Message "Peuplement de la base de données..." -Type Info
        docker-compose exec -T app php artisan db:seed --force

        # Passport
        Write-Message "Installation de Laravel Passport..." -Type Info
        docker-compose exec -T app php artisan passport:install --force

        Write-Host ""
        Write-Message "Backend déployé avec succès!" -Type Success
        Write-Message "URL: http://localhost:$($Script:Config.Backend.Port)" -Type Info
        Write-Host ""

        return $true

    } catch {
        Write-Message "Erreur lors du déploiement du backend: $_" -Type Error
        return $false
    } finally {
        Pop-Location
    }
}

# Fonction pour déployer un frontend
function Deploy-Frontend {
    param(
        [string]$ComponentName
    )

    $config = $Script:Config[$ComponentName]
    Write-Message "Déploiement de $($config.Name)..." -Type Header
    Write-Host ""

    $componentPath = $config.Path

    if (-not (Test-Path $componentPath)) {
        Write-Message "Dossier introuvable: $componentPath" -Type Error
        return $false
    }

    Push-Location $componentPath

    try {
        # Créer le fichier .env
        $envVars = @{
            'VITE_API_URL' = 'http://localhost:8004/api'
        }

        switch ($ComponentName) {
            'Portal' {
                $envVars['VITE_PORTAL_URL'] = 'http://localhost:3000'
                $envVars['VITE_ADMIN_URL'] = 'http://localhost:3000'
            }
            'Backoffice' {
                $envVars['VITE_ADMIN_URL'] = 'http://localhost:3000'
            }
            'Affiliate' {
                $envVars['VITE_AFFILIATE_URL'] = 'http://localhost:5173'
            }
        }

        Initialize-Environment -ComponentPath "." -EnvVars $envVars

        # Installer les dépendances
        Write-Message "Installation des dépendances..." -Type Info

        if ($config.RequiresPnpm) {
            if (-not (Test-Command "pnpm")) {
                Write-Message "pnpm requis mais non installé. Installez avec: npm install -g pnpm" -Type Error
                return $false
            }
            pnpm install
        } else {
            npm install
        }

        if ($LASTEXITCODE -ne 0) {
            Write-Message "Erreur lors de l'installation des dépendances" -Type Error
            return $false
        }

        Write-Host ""
        Write-Message "$($config.Name) configuré avec succès!" -Type Success
        Write-Message "Pour démarrer, exécutez dans ce dossier:" -Type Info

        if ($config.RequiresPnpm) {
            Write-Host "  pnpm dev" -ForegroundColor $Script:Colors.Info
        } else {
            Write-Host "  npm run dev" -ForegroundColor $Script:Colors.Info
        }

        # Gérer le conflit de port pour le Backoffice
        if ($ComponentName -eq 'Backoffice') {
            Write-Message "Note: Le Backoffice utilise le port 3000 (même que le Portal)" -Type Warning
            Write-Message "Pour utiliser un autre port: pnpm dev -- --port 3001" -Type Info
        }

        Write-Message "URL: http://localhost:$($config.Port)" -Type Info
        Write-Host ""

        return $true

    } catch {
        Write-Message "Erreur lors du déploiement: $_" -Type Error
        return $false
    } finally {
        Pop-Location
    }
}

# Fonction pour déployer tous les composants
function Deploy-All {
    Write-Message "Déploiement de tous les composants..." -Type Header
    Write-Host ""

    # Backend d'abord
    if (-not (Deploy-Backend)) {
        Write-Message "Échec du déploiement du backend" -Type Error
        return $false
    }

    Write-Host ""
    Write-Host "═══════════════════════════════════════════════════" -ForegroundColor $Script:Colors.Header
    Write-Host ""

    # Puis les frontends
    foreach ($component in @('Portal', 'Backoffice', 'Affiliate')) {
        if (-not (Deploy-Frontend -ComponentName $component)) {
            Write-Message "Échec du déploiement de $component" -Type Warning
        }
        Write-Host ""
        Write-Host "═══════════════════════════════════════════════════" -ForegroundColor $Script:Colors.Header
        Write-Host ""
    }

    Write-Message "Configuration terminée!" -Type Success
    Write-Host ""
    Write-Message "Prochaines étapes:" -Type Info
    Write-Host "  1. Le backend est démarré automatiquement" -ForegroundColor $Script:Colors.Info
    Write-Host "  2. Pour démarrer un frontend, naviguez vers son dossier et exécutez:" -ForegroundColor $Script:Colors.Info
    Write-Host "     - Portal: cd simveb-portal-design-develop && npm run dev" -ForegroundColor $Script:Colors.Info
    Write-Host "     - Backoffice: cd simveb-backoffice-develop && pnpm dev -- --port 3001" -ForegroundColor $Script:Colors.Info
    Write-Host "     - Affiliate: cd simveb-affiliate-develop && pnpm dev" -ForegroundColor $Script:Colors.Info
    Write-Host ""

    return $true
}

# Fonction pour arrêter tous les services
function Stop-Services {
    Write-Message "Arrêt de tous les services..." -Type Header
    Write-Host ""

    # Arrêter le backend Docker
    $backendPath = $Script:Config.Backend.Path
    if (Test-Path $backendPath) {
        Push-Location $backendPath
        Write-Message "Arrêt des conteneurs Docker..." -Type Info
        docker-compose down
        Pop-Location
        Write-Message "Conteneurs Docker arrêtés" -Type Success
    }

    # Arrêter les processus Node.js
    Write-Message "Arrêt des processus Node.js..." -Type Info
    $nodeProcesses = Get-Process node -ErrorAction SilentlyContinue
    if ($nodeProcesses) {
        $nodeProcesses | Stop-Process -Force
        Write-Message "Processus Node.js arrêtés" -Type Success
    } else {
        Write-Message "Aucun processus Node.js en cours d'exécution" -Type Info
    }

    Write-Host ""
    Write-Message "Tous les services ont été arrêtés" -Type Success
}

# Fonction pour afficher les logs
function Show-Logs {
    Write-Message "Logs du Backend..." -Type Header
    Write-Host ""

    $backendPath = $Script:Config.Backend.Path
    if (Test-Path $backendPath) {
        Push-Location $backendPath
        Write-Message "Affichage des logs Docker (Ctrl+C pour quitter)..." -Type Info
        docker-compose logs -f --tail=50
        Pop-Location
    } else {
        Write-Message "Dossier backend introuvable" -Type Error
    }
}

# Fonction pour afficher le menu principal
function Show-Menu {
    Show-Header

    Write-Host "Choisissez une option:" -ForegroundColor $Script:Colors.Info
    Write-Host ""
    Write-Host "  [1] Déploiement complet (Backend + tous les frontends)" -ForegroundColor White
    Write-Host "  [2] Backend uniquement" -ForegroundColor White
    Write-Host "  [3] Portal uniquement" -ForegroundColor White
    Write-Host "  [4] Backoffice uniquement" -ForegroundColor White
    Write-Host "  [5] Affiliate uniquement" -ForegroundColor White
    Write-Host "  [6] Vérifier les prérequis" -ForegroundColor White
    Write-Host "  [7] Arrêter tous les services" -ForegroundColor White
    Write-Host "  [8] Voir les logs du backend" -ForegroundColor White
    Write-Host "  [Q] Quitter" -ForegroundColor White
    Write-Host ""

    $choice = Read-Host "Votre choix"

    switch ($choice.ToUpper()) {
        '1' {
            Show-Header
            if (-not $SkipChecks) {
                if (-not (Test-Prerequisites)) {
                    Read-Host "`nAppuyez sur Entrée pour revenir au menu"
                    Show-Menu
                    return
                }
                Write-Host ""
            }
            Deploy-All
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '2' {
            Show-Header
            Deploy-Backend
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '3' {
            Show-Header
            Deploy-Frontend -ComponentName 'Portal'
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '4' {
            Show-Header
            Deploy-Frontend -ComponentName 'Backoffice'
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '5' {
            Show-Header
            Deploy-Frontend -ComponentName 'Affiliate'
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '6' {
            Show-Header
            Test-Prerequisites
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '7' {
            Show-Header
            Stop-Services
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '8' {
            Show-Header
            Show-Logs
            Show-Menu
        }
        'Q' {
            Write-Host ""
            Write-Message "Au revoir!" -Type Success
            Write-Host ""
            exit 0
        }
        default {
            Write-Message "Option invalide" -Type Error
            Start-Sleep -Seconds 1
            Show-Menu
        }
    }
}

# Point d'entrée principal
function Main {
    # Vérifier si on est dans le bon dossier
    if (-not (Test-Path "simveb-backend-develop") -and
        -not (Test-Path "simveb-portal-design-develop") -and
        -not (Test-Path "simveb-backoffice-develop")) {
        Write-Message "Ce script doit être exécuté depuis le dossier racine du projet" -Type Error
        exit 1
    }

    # Si un composant est spécifié, le déployer directement
    if ($Component -ne 'menu') {
        Show-Header

        if (-not $SkipChecks) {
            if (-not (Test-Prerequisites)) {
                exit 1
            }
            Write-Host ""
        }

        switch ($Component) {
            'all' { Deploy-All }
            'backend' { Deploy-Backend }
            'portal' { Deploy-Frontend -ComponentName 'Portal' }
            'backoffice' { Deploy-Frontend -ComponentName 'Backoffice' }
            'affiliate' { Deploy-Frontend -ComponentName 'Affiliate' }
        }
    } else {
        # Afficher le menu interactif
        Show-Menu
    }
}

# Exécuter le script
Main
