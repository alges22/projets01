#Requires -Version 5.1

<#
.SYNOPSIS
    Script de déploiement de l'Affiliate SIMVEB

.DESCRIPTION
    Déploie et configure l'Affiliate (Vue 3 + Vite)

.EXAMPLE
    .\deploy-affiliate.ps1

.EXAMPLE
    .\deploy-affiliate.ps1 -Start
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
$affiliatePath = "..\simveb-affiliate-develop"

if (Test-Path "simveb-affiliate-develop") {
    $affiliatePath = "simveb-affiliate-develop"
}

if (-not (Test-Path $affiliatePath)) {
    Write-Error "Dossier affiliate introuvable: $affiliatePath"
    Write-Host "Exécutez ce script depuis le dossier racine du projet ou depuis le dossier scripts/"
    exit 1
}

Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║       Déploiement Affiliate SIMVEB            ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor Magenta

Push-Location $affiliatePath

try {
    # Étape 1: Vérifier pnpm
    Write-Step "Vérification de pnpm..."
    try {
        $pnpmVersion = pnpm --version
        Write-Success "pnpm installé: v$pnpmVersion"
    } catch {
        Write-Error "pnpm n'est pas installé"
        Write-Host "`nL'Affiliate nécessite pnpm (pas npm ou yarn)" -ForegroundColor Yellow
        Write-Host "Installez avec: npm install -g pnpm" -ForegroundColor Cyan
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
    Write-Step "Installation des dépendances pnpm..."
    Write-Host "Cela peut prendre plusieurs minutes..." -ForegroundColor Yellow
    pnpm install
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Échec de l'installation des dépendances"
        exit 1
    }
    Write-Success "Dépendances installées"

    # Succès
    Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor Green
    Write-Host "║        Configuration réussie!                  ║" -ForegroundColor Green
    Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor Green

    Write-Host "Pour démarrer l'Affiliate:" -ForegroundColor Yellow
    Write-Host "  cd $affiliatePath"
    Write-Host "  pnpm dev" -ForegroundColor Cyan

    Write-Host "`nAffiliate sera disponible sur: " -NoNewline
    Write-Host "http://localhost:5173" -ForegroundColor Cyan

    Write-Host "`nCommandes utiles:" -ForegroundColor Yellow
    Write-Host "  Mode développement:      pnpm dev"
    Write-Host "  Build production:        pnpm build"
    Write-Host "  Preview production:      pnpm preview"
    Write-Host ""

    # Démarrer automatiquement si demandé
    if ($Start) {
        Write-Host "`nDémarrage du serveur de développement..." -ForegroundColor Yellow
        Write-Host "Appuyez sur Ctrl+C pour arrêter`n" -ForegroundColor Yellow
        pnpm dev
    }

} catch {
    Write-Error "Erreur lors de la configuration: $_"
    exit 1
} finally {
    Pop-Location
}
