#Requires -Version 5.1

<#
.SYNOPSIS
    Script de déploiement du Backend SIMVEB

.DESCRIPTION
    Déploie et configure le backend Laravel avec Docker

.EXAMPLE
    .\deploy-backend.ps1
#>

param(
    [Parameter(Mandatory=$false)]
    [switch]$SkipEnv
)

$ErrorActionPreference = "Stop"

# Couleurs
$Success = 'Green'
$Error = 'Red'
$Warning = 'Yellow'
$Info = 'Cyan'

function Write-Step {
    param([string]$Message)
    Write-Host "`n[>] $Message" -ForegroundColor $Info
}

function Write-Success {
    param([string]$Message)
    Write-Host "[✓] $Message" -ForegroundColor $Success
}

function Write-Error {
    param([string]$Message)
    Write-Host "[✗] $Message" -ForegroundColor $Error
}

# Vérifier qu'on est dans le bon dossier
$backendPath = "..\simveb-backend-develop"

if (Test-Path "simveb-backend-develop") {
    $backendPath = "simveb-backend-develop"
}

if (-not (Test-Path $backendPath)) {
    Write-Error "Dossier backend introuvable: $backendPath"
    Write-Host "Exécutez ce script depuis le dossier racine du projet ou depuis le dossier scripts/"
    exit 1
}

Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║    Déploiement Backend SIMVEB (Laravel)        ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor Magenta

Push-Location $backendPath

try {
    # Étape 1: Vérifier Docker
    Write-Step "Vérification de Docker..."
    try {
        $null = docker ps 2>$null
        Write-Success "Docker est en cours d'exécution"
    } catch {
        Write-Error "Docker n'est pas démarré. Démarrez Docker Desktop et réessayez."
        exit 1
    }

    # Étape 2: Créer le fichier .env
    if (-not $SkipEnv) {
        Write-Step "Configuration de l'environnement..."
        if (-not (Test-Path ".env")) {
            if (Test-Path ".env.example") {
                Copy-Item ".env.example" ".env"
                Write-Success "Fichier .env créé depuis .env.example"

                # Configurer les variables pour Docker
                $envContent = Get-Content ".env"
                $envContent = $envContent -replace "^DB_HOST=.*", "DB_HOST=db"
                $envContent = $envContent -replace "^DB_PORT=.*", "DB_PORT=5432"
                $envContent = $envContent -replace "^DB_DATABASE=.*", "DB_DATABASE=simveb"
                $envContent = $envContent -replace "^DB_USERNAME=.*", "DB_USERNAME=simveb"
                $envContent = $envContent -replace "^DB_PASSWORD=.*", "DB_PASSWORD=password"
                $envContent | Set-Content ".env"
                Write-Success "Variables d'environnement configurées"
            } else {
                Write-Error "Fichier .env.example introuvable"
                exit 1
            }
        } else {
            Write-Success "Fichier .env existe déjà"
        }
    }

    # Étape 3: Démarrer Docker Compose
    Write-Step "Démarrage des conteneurs Docker..."
    docker-compose up -d
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Échec du démarrage des conteneurs"
        exit 1
    }
    Write-Success "Conteneurs démarrés"

    # Attendre que les conteneurs soient prêts
    Write-Step "Attente du démarrage des services (15 secondes)..."
    Start-Sleep -Seconds 15
    Write-Success "Services prêts"

    # Étape 4: Installer les dépendances
    Write-Step "Installation des dépendances Composer..."
    docker-compose exec -T app composer install --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Échec de l'installation des dépendances"
        exit 1
    }
    Write-Success "Dépendances installées"

    # Étape 5: Générer la clé d'application
    Write-Step "Génération de la clé d'application..."
    docker-compose exec -T app php artisan key:generate --force
    Write-Success "Clé d'application générée"

    # Étape 6: Créer le lien symbolique de stockage
    Write-Step "Création du lien symbolique de stockage..."
    docker-compose exec -T app php artisan storage:link
    Write-Success "Lien symbolique créé"

    # Étape 7: Migrations de base de données
    Write-Step "Exécution des migrations..."
    docker-compose exec -T app php artisan migrate --force
    if ($LASTEXITCODE -ne 0) {
        Write-Warning "Erreur lors des migrations (peut-être déjà exécutées)"
    } else {
        Write-Success "Migrations exécutées"
    }

    # Étape 8: Seeding de la base de données
    Write-Step "Peuplement de la base de données..."
    docker-compose exec -T app php artisan db:seed --force
    if ($LASTEXITCODE -ne 0) {
        Write-Warning "Erreur lors du seeding (peut-être déjà effectué)"
    } else {
        Write-Success "Base de données peuplée"
    }

    # Étape 9: Installation de Passport
    Write-Step "Installation de Laravel Passport..."
    docker-compose exec -T app php artisan passport:install --force
    if ($LASTEXITCODE -ne 0) {
        Write-Warning "Erreur lors de l'installation de Passport (peut-être déjà installé)"
    } else {
        Write-Success "Laravel Passport installé"
    }

    # Étape 10: Optimisations (optionnel en dev)
    Write-Step "Optimisations..."
    docker-compose exec -T app php artisan config:clear
    docker-compose exec -T app php artisan cache:clear
    docker-compose exec -T app php artisan route:clear
    docker-compose exec -T app php artisan view:clear
    Write-Success "Caches nettoyés"

    # Succès
    Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor Green
    Write-Host "║          Déploiement réussi!                   ║" -ForegroundColor Green
    Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor Green

    Write-Host "Backend disponible sur: " -NoNewline
    Write-Host "http://localhost:8004" -ForegroundColor Cyan

    Write-Host "`nCommandes utiles:" -ForegroundColor Yellow
    Write-Host "  Voir les logs:           docker-compose logs -f app"
    Write-Host "  Arrêter:                 docker-compose down"
    Write-Host "  Redémarrer:              docker-compose restart"
    Write-Host "  Accéder au conteneur:    docker-compose exec app bash"
    Write-Host "  Voir les conteneurs:     docker-compose ps"
    Write-Host ""

} catch {
    Write-Error "Erreur lors du déploiement: $_"
    Write-Host "`nPour voir les logs détaillés:" -ForegroundColor Yellow
    Write-Host "  docker-compose logs app" -ForegroundColor Yellow
    exit 1
} finally {
    Pop-Location
}
