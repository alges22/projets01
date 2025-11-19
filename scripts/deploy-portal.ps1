#Requires -Version 5.1

<#
.SYNOPSIS
    Script de déploiement du Portal SIMVEB

.DESCRIPTION
    Déploie et configure le Portal (Nuxt.js)

.EXAMPLE
    .\deploy-portal.ps1
#>

param(
    [Parameter(Mandatory=$false)]
    [switch]$SkipEnv,

    [Parameter(Mandatory=$false)]
    [switch]$Start
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
$portalPath = "..\simveb-portal-design-develop"

if (Test-Path "simveb-portal-design-develop") {
    $portalPath = "simveb-portal-design-develop"
}

if (-not (Test-Path $portalPath)) {
    Write-Error "Dossier portal introuvable: $portalPath"
    Write-Host "Exécutez ce script depuis le dossier racine du projet ou depuis le dossier scripts/"
    exit 1
}

Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║      Déploiement Portal SIMVEB (Nuxt.js)      ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor Magenta

Push-Location $portalPath

try {
    # Étape 1: Vérifier Node.js
    Write-Step "Vérification de Node.js..."
    try {
        $nodeVersion = node --version
        Write-Success "Node.js installé: $nodeVersion"
    } catch {
        Write-Error "Node.js n'est pas installé. Téléchargez depuis https://nodejs.org/"
        exit 1
    }

    # Étape 2: Créer le fichier .env
    if (-not $SkipEnv) {
        Write-Step "Configuration de l'environnement..."
        if (-not (Test-Path ".env")) {
            if (Test-Path ".env.example") {
                Copy-Item ".env.example" ".env"
                Write-Success "Fichier .env créé depuis .env.example"

                # Configurer les variables
                $envContent = Get-Content ".env"
                $envContent = $envContent -replace "^VITE_API_URL=.*", "VITE_API_URL=http://localhost:8004/api"
                $envContent = $envContent -replace "^VITE_PORTAL_URL=.*", "VITE_PORTAL_URL=http://localhost:3000"
                $envContent = $envContent -replace "^VITE_ADMIN_URL=.*", "VITE_ADMIN_URL=http://localhost:3000"
                $envContent = $envContent -replace "^VITE_AFFILIATE_URL=.*", "VITE_AFFILIATE_URL=http://localhost:5173"
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

    # Étape 3: Installer les dépendances
    Write-Step "Installation des dépendances npm..."
    Write-Host "Cela peut prendre plusieurs minutes..." -ForegroundColor Yellow
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Échec de l'installation des dépendances"
        exit 1
    }
    Write-Success "Dépendances installées"

    # Succès
    Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor Green
    Write-Host "║        Configuration réussie!                  ║" -ForegroundColor Green
    Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor Green

    Write-Host "Pour démarrer le Portal:" -ForegroundColor Yellow
    Write-Host "  cd $portalPath"
    Write-Host "  npm run dev" -ForegroundColor Cyan

    Write-Host "`nPortal sera disponible sur: " -NoNewline
    Write-Host "http://localhost:3000" -ForegroundColor Cyan

    Write-Host "`nCommandes utiles:" -ForegroundColor Yellow
    Write-Host "  Mode développement:      npm run dev"
    Write-Host "  Build production:        npm run build"
    Write-Host "  Démarrer production:     npm run start"
    Write-Host "  Générer statique:        npm run generate"
    Write-Host ""

    # Démarrer automatiquement si demandé
    if ($Start) {
        Write-Host "`nDémarrage du serveur de développement..." -ForegroundColor Yellow
        Write-Host "Appuyez sur Ctrl+C pour arrêter`n" -ForegroundColor Yellow
        npm run dev
    }

} catch {
    Write-Error "Erreur lors de la configuration: $_"
    exit 1
} finally {
    Pop-Location
}
