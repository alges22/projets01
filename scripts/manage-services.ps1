#Requires -Version 5.1

<#
.SYNOPSIS
    Script de gestion des services SIMVEB

.DESCRIPTION
    Permet de démarrer, arrêter et monitorer les services SIMVEB

.PARAMETER Action
    Action à effectuer: start, stop, restart, status, logs

.PARAMETER Service
    Service à gérer: backend, portal, backoffice, affiliate, all

.EXAMPLE
    .\manage-services.ps1 -Action stop -Service all
    Arrête tous les services

.EXAMPLE
    .\manage-services.ps1 -Action logs -Service backend
    Affiche les logs du backend

.EXAMPLE
    .\manage-services.ps1 -Action status
    Affiche le statut de tous les services
#>

param(
    [Parameter(Mandatory=$false)]
    [ValidateSet('start', 'stop', 'restart', 'status', 'logs', 'menu')]
    [string]$Action = 'menu',

    [Parameter(Mandatory=$false)]
    [ValidateSet('backend', 'portal', 'backoffice', 'affiliate', 'all')]
    [string]$Service = 'all'
)

$ErrorActionPreference = "Stop"

# Couleurs
$Success = 'Green'
$Error = 'Red'
$Warning = 'Yellow'
$Info = 'Cyan'
$Header = 'Magenta'

function Write-Header {
    param([string]$Message)
    Write-Host "`n╔════════════════════════════════════════════════╗" -ForegroundColor $Header
    Write-Host "║  $($Message.PadRight(46))║" -ForegroundColor $Header
    Write-Host "╚════════════════════════════════════════════════╝`n" -ForegroundColor $Header
}

function Write-Success {
    param([string]$Message)
    Write-Host "[✓] $Message" -ForegroundColor $Success
}

function Write-Error {
    param([string]$Message)
    Write-Host "[✗] $Message" -ForegroundColor $Error
}

function Write-Info {
    param([string]$Message)
    Write-Host "[i] $Message" -ForegroundColor $Info
}

function Test-Port {
    param([int]$Port)
    $connection = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
    return $null -ne $connection
}

function Get-ServiceStatus {
    Write-Header "Statut des services SIMVEB"

    # Backend (Docker)
    Write-Host "Backend API (Port 8004):" -NoNewline
    $backendPath = "simveb-backend-develop"
    if (Test-Path $backendPath) {
        Push-Location $backendPath
        $containers = docker-compose ps -q 2>$null
        Pop-Location

        if ($containers) {
            Write-Host " EN COURS D'EXÉCUTION" -ForegroundColor $Success
            if (Test-Port -Port 8004) {
                Write-Success "  Port 8004 accessible"
            }
        } else {
            Write-Host " ARRÊTÉ" -ForegroundColor $Warning
        }
    } else {
        Write-Host " DOSSIER INTROUVABLE" -ForegroundColor $Error
    }

    # Portal
    Write-Host "`nPortal (Port 3000):" -NoNewline
    if (Test-Port -Port 3000) {
        Write-Host " EN COURS D'EXÉCUTION" -ForegroundColor $Success
    } else {
        Write-Host " ARRÊTÉ" -ForegroundColor $Warning
    }

    # Backoffice (peut être sur 3000 ou 3001)
    Write-Host "`nBackoffice (Port 3000/3001):" -NoNewline
    $backofficePorts = @(3000, 3001)
    $running = $false
    foreach ($port in $backofficePorts) {
        if (Test-Port -Port $port) {
            Write-Host " EN COURS D'EXÉCUTION (port $port)" -ForegroundColor $Success
            $running = $true
            break
        }
    }
    if (-not $running) {
        Write-Host " ARRÊTÉ" -ForegroundColor $Warning
    }

    # Affiliate
    Write-Host "`nAffiliate (Port 5173):" -NoNewline
    if (Test-Port -Port 5173) {
        Write-Host " EN COURS D'EXÉCUTION" -ForegroundColor $Success
    } else {
        Write-Host " ARRÊTÉ" -ForegroundColor $Warning
    }

    # Processus Node.js
    Write-Host "`nProcessus Node.js actifs:" -ForegroundColor $Info
    $nodeProcesses = Get-Process node -ErrorAction SilentlyContinue
    if ($nodeProcesses) {
        Write-Host "  Nombre de processus: $($nodeProcesses.Count)" -ForegroundColor $Info
        $nodeProcesses | Select-Object Id, CPU, WorkingSet | Format-Table
    } else {
        Write-Host "  Aucun processus Node.js" -ForegroundColor $Warning
    }

    # Conteneurs Docker
    Write-Host "Conteneurs Docker actifs:" -ForegroundColor $Info
    $backendPath = "simveb-backend-develop"
    if (Test-Path $backendPath) {
        Push-Location $backendPath
        docker-compose ps
        Pop-Location
    }
}

function Stop-AllServices {
    Write-Header "Arrêt de tous les services"

    # Arrêter le backend Docker
    Write-Info "Arrêt du backend..."
    $backendPath = "simveb-backend-develop"
    if (Test-Path $backendPath) {
        Push-Location $backendPath
        docker-compose down
        Pop-Location
        Write-Success "Backend arrêté"
    } else {
        Write-Error "Dossier backend introuvable"
    }

    # Arrêter les processus Node.js
    Write-Info "Arrêt des processus Node.js..."
    $nodeProcesses = Get-Process node -ErrorAction SilentlyContinue
    if ($nodeProcesses) {
        $nodeProcesses | Stop-Process -Force
        Write-Success "Processus Node.js arrêtés ($($nodeProcesses.Count))"
    } else {
        Write-Info "Aucun processus Node.js à arrêter"
    }

    Write-Host ""
    Write-Success "Tous les services ont été arrêtés"
}

function Stop-Backend {
    Write-Header "Arrêt du Backend"

    $backendPath = "simveb-backend-develop"
    if (Test-Path $backendPath) {
        Push-Location $backendPath
        docker-compose down
        Pop-Location
        Write-Success "Backend arrêté"
    } else {
        Write-Error "Dossier backend introuvable"
    }
}

function Show-Logs {
    param([string]$ServiceName = 'backend')

    Write-Header "Logs - $ServiceName"

    if ($ServiceName -eq 'backend') {
        $backendPath = "simveb-backend-develop"
        if (Test-Path $backendPath) {
            Push-Location $backendPath
            Write-Info "Affichage des logs (Ctrl+C pour quitter)..."
            Write-Host ""
            docker-compose logs -f --tail=100
            Pop-Location
        } else {
            Write-Error "Dossier backend introuvable"
        }
    } else {
        Write-Info "Les logs des frontends sont affichés dans leur terminal respectif"
    }
}

function Restart-Service {
    param([string]$ServiceName)

    if ($ServiceName -eq 'backend') {
        Write-Header "Redémarrage du Backend"

        $backendPath = "simveb-backend-develop"
        if (Test-Path $backendPath) {
            Push-Location $backendPath
            docker-compose restart
            Pop-Location
            Write-Success "Backend redémarré"
        } else {
            Write-Error "Dossier backend introuvable"
        }
    } else {
        Write-Info "Les frontends doivent être redémarrés manuellement (Ctrl+C puis relancer)"
    }
}

function Show-Menu {
    Clear-Host
    Write-Host ""
    Write-Host "╔════════════════════════════════════════════════╗" -ForegroundColor $Header
    Write-Host "║     SIMVEB - Gestion des Services             ║" -ForegroundColor $Header
    Write-Host "╚════════════════════════════════════════════════╝" -ForegroundColor $Header
    Write-Host ""

    Write-Host "Choisissez une option:" -ForegroundColor $Info
    Write-Host ""
    Write-Host "  [1] Voir le statut de tous les services" -ForegroundColor White
    Write-Host "  [2] Arrêter tous les services" -ForegroundColor White
    Write-Host "  [3] Arrêter le backend uniquement" -ForegroundColor White
    Write-Host "  [4] Redémarrer le backend" -ForegroundColor White
    Write-Host "  [5] Voir les logs du backend" -ForegroundColor White
    Write-Host "  [6] Nettoyer les conteneurs Docker" -ForegroundColor White
    Write-Host "  [Q] Quitter" -ForegroundColor White
    Write-Host ""

    $choice = Read-Host "Votre choix"

    switch ($choice.ToUpper()) {
        '1' {
            Get-ServiceStatus
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '2' {
            Stop-AllServices
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '3' {
            Stop-Backend
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '4' {
            Restart-Service -ServiceName 'backend'
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        '5' {
            Show-Logs -ServiceName 'backend'
            Show-Menu
        }
        '6' {
            Write-Header "Nettoyage Docker"
            Write-Warning "Cela va supprimer tous les conteneurs, images et volumes non utilisés"
            $confirm = Read-Host "Continuer? (O/N)"
            if ($confirm -eq 'O' -or $confirm -eq 'o') {
                docker system prune -a --volumes -f
                Write-Success "Nettoyage terminé"
            }
            Read-Host "`nAppuyez sur Entrée pour revenir au menu"
            Show-Menu
        }
        'Q' {
            Write-Host ""
            Write-Success "Au revoir!"
            Write-Host ""
            exit 0
        }
        default {
            Write-Error "Option invalide"
            Start-Sleep -Seconds 1
            Show-Menu
        }
    }
}

# Point d'entrée principal
function Main {
    if ($Action -eq 'menu') {
        Show-Menu
    } else {
        switch ($Action) {
            'status' { Get-ServiceStatus }
            'stop' {
                if ($Service -eq 'all') {
                    Stop-AllServices
                } elseif ($Service -eq 'backend') {
                    Stop-Backend
                } else {
                    Write-Info "Seul le backend peut être arrêté via ce script"
                    Write-Info "Les frontends doivent être arrêtés avec Ctrl+C dans leur terminal"
                }
            }
            'restart' {
                if ($Service -eq 'backend' -or $Service -eq 'all') {
                    Restart-Service -ServiceName 'backend'
                } else {
                    Write-Info "Seul le backend peut être redémarré via ce script"
                }
            }
            'logs' {
                Show-Logs -ServiceName $Service
            }
            'start' {
                Write-Info "Pour démarrer les services, utilisez les scripts deploy-*.ps1"
            }
        }
    }
}

# Exécuter
Main
