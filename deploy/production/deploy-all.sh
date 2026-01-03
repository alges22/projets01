#!/bin/bash

# ========================================
# SIMVEB - Script de Déploiement PRODUCTION
# Déploie tous les composants sur production
# ========================================

set -e  # Exit on error

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

# Variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="/opt/simveb"
DEPLOY_USER="simveb"
DOCKER_COMPOSE_FILE="${PROJECT_ROOT}/deploy/production/docker-compose.yml"
ENV_FILE="${PROJECT_ROOT}/.env.production"

# Logging functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_critical() {
    echo -e "${MAGENTA}[CRITICAL]${NC} $1"
}

# Check if running as deploy user
check_user() {
    if [ "$(whoami)" != "$DEPLOY_USER" ]; then
        log_error "This script must be run as $DEPLOY_USER user"
        exit 1
    fi
}

# Production safety check
production_safety_check() {
    log_critical "========================================="
    log_critical "  PRODUCTION DEPLOYMENT WARNING"
    log_critical "========================================="
    log_warning "You are about to deploy to PRODUCTION!"
    log_warning "This will affect live users."
    echo ""
    read -p "Are you sure you want to continue? (type 'yes' to confirm): " confirm

    if [ "$confirm" != "yes" ]; then
        log_info "Deployment cancelled by user"
        exit 0
    fi

    log_info "Production deployment confirmed"
}

# Check prerequisites
check_prerequisites() {
    log_info "Checking prerequisites..."

    # Check Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed"
        exit 1
    fi

    # Check Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose is not installed"
        exit 1
    fi

    # Check if Docker is running
    if ! docker info &> /dev/null; then
        log_error "Docker daemon is not running"
        exit 1
    fi

    # Check available disk space (minimum 10GB)
    available_space=$(df /opt | tail -1 | awk '{print $4}')
    if [ "$available_space" -lt 10485760 ]; then
        log_error "Insufficient disk space (less than 10GB available)"
        exit 1
    fi

    log_success "All prerequisites met"
}

# Load environment variables
load_env() {
    log_info "Loading environment variables..."

    if [ -f "$ENV_FILE" ]; then
        set -a
        source "$ENV_FILE"
        set +a
        log_success "Environment variables loaded"
    else
        log_error "Environment file not found: $ENV_FILE"
        exit 1
    fi
}

# Login to GitLab Container Registry
docker_login() {
    log_info "Logging in to GitLab Container Registry..."

    if [ -z "$CI_REGISTRY_USER" ] || [ -z "$CI_REGISTRY_PASSWORD" ]; then
        log_error "CI_REGISTRY_USER or CI_REGISTRY_PASSWORD not set"
        exit 1
    fi

    echo "$CI_REGISTRY_PASSWORD" | docker login -u "$CI_REGISTRY_USER" --password-stdin $CI_REGISTRY

    log_success "Docker login successful"
}

# Pull latest images
pull_images() {
    log_info "Pulling latest Docker images..."

    cd "$PROJECT_ROOT"
    docker-compose -f "$DOCKER_COMPOSE_FILE" pull

    log_success "Images pulled successfully"
}

# Backup database (MANDATORY for production)
backup_database() {
    log_info "Creating MANDATORY database backup..."

    BACKUP_DIR="${PROJECT_ROOT}/backups"
    TIMESTAMP=$(date +%Y%m%d-%H%M%S)
    BACKUP_FILE="${BACKUP_DIR}/simveb_production_${TIMESTAMP}.sql"

    mkdir -p "$BACKUP_DIR"

    # Run backup script
    if [ -f "${PROJECT_ROOT}/deploy/database/backup-db.sh" ]; then
        bash "${PROJECT_ROOT}/deploy/database/backup-db.sh" production "$BACKUP_FILE"

        # Verify backup was created
        if [ -f "$BACKUP_FILE" ]; then
            BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
            log_success "Database backup created: $BACKUP_FILE (Size: $BACKUP_SIZE)"

            # Store backup path for potential rollback
            echo "$BACKUP_FILE" > "${BACKUP_DIR}/latest_backup.txt"
        else
            log_error "Database backup failed!"
            exit 1
        fi
    else
        log_error "Backup script not found! Cannot proceed without backup."
        exit 1
    fi
}

# Enable maintenance mode
enable_maintenance_mode() {
    log_info "Enabling maintenance mode..."

    if docker ps | grep -q simveb-backend-prod; then
        docker exec simveb-backend-prod php artisan down --retry=60 || true
        log_success "Maintenance mode enabled"
    else
        log_warning "Backend container not running, skipping maintenance mode"
    fi
}

# Disable maintenance mode
disable_maintenance_mode() {
    log_info "Disabling maintenance mode..."

    if docker ps | grep -q simveb-backend-prod; then
        docker exec simveb-backend-prod php artisan up || true
        log_success "Maintenance mode disabled"
    fi
}

# Stop old containers (gracefully)
stop_containers() {
    log_info "Gracefully stopping containers..."

    cd "$PROJECT_ROOT"

    # Give containers time to finish requests
    docker-compose -f "$DOCKER_COMPOSE_FILE" stop -t 30

    # Remove containers
    docker-compose -f "$DOCKER_COMPOSE_FILE" down --remove-orphans

    log_success "Old containers stopped"
}

# Start new containers
start_containers() {
    log_info "Starting new containers..."

    cd "$PROJECT_ROOT"
    docker-compose -f "$DOCKER_COMPOSE_FILE" up -d

    log_success "New containers started"
}

# Wait for services to be ready
wait_for_services() {
    log_info "Waiting for services to be ready..."

    local max_attempts=30
    local attempt=0

    while [ $attempt -lt $max_attempts ]; do
        if docker exec simveb-backend-prod php artisan health:check &> /dev/null; then
            log_success "Backend is ready"
            return 0
        fi

        attempt=$((attempt + 1))
        echo -n "."
        sleep 2
    done

    log_error "Services failed to start within expected time"
    return 1
}

# Run migrations
run_migrations() {
    log_info "Running database migrations..."

    # Wait for backend to be ready
    sleep 10

    docker exec simveb-backend-prod php artisan migrate --force

    log_success "Migrations completed"
}

# Clear and warm cache
optimize_application() {
    log_info "Optimizing application..."

    docker exec simveb-backend-prod php artisan config:cache
    docker exec simveb-backend-prod php artisan route:cache
    docker exec simveb-backend-prod php artisan view:cache
    docker exec simveb-backend-prod php artisan optimize

    log_success "Application optimized"
}

# Health check
health_check() {
    log_info "Running comprehensive health checks..."

    local errors=0

    # Check backend
    if curl -f -s http://localhost:8080/health > /dev/null; then
        log_success "Backend is healthy"
    else
        log_error "Backend health check failed"
        errors=$((errors + 1))
    fi

    # Check portal
    if curl -f -s http://localhost:3000 > /dev/null; then
        log_success "Portal is healthy"
    else
        log_error "Portal health check failed"
        errors=$((errors + 1))
    fi

    # Check backoffice
    if curl -f -s http://localhost:3001 > /dev/null; then
        log_success "Backoffice is healthy"
    else
        log_error "Backoffice health check failed"
        errors=$((errors + 1))
    fi

    # Check affiliate
    if curl -f -s http://localhost:3002 > /dev/null; then
        log_success "Affiliate is healthy"
    else
        log_error "Affiliate health check failed"
        errors=$((errors + 1))
    fi

    # Check Redis
    if docker exec simveb-redis-prod redis-cli ping > /dev/null 2>&1; then
        log_success "Redis is healthy"
    else
        log_error "Redis health check failed"
        errors=$((errors + 1))
    fi

    # Check database connectivity
    if docker exec simveb-backend-prod php artisan db:show > /dev/null 2>&1; then
        log_success "Database connection is healthy"
    else
        log_error "Database connection failed"
        errors=$((errors + 1))
    fi

    if [ $errors -gt 0 ]; then
        log_error "Health check failed with $errors error(s)"
        return 1
    fi

    return 0
}

# Show deployment status
show_status() {
    log_info "Deployment Status:"
    echo ""
    docker-compose -f "$DOCKER_COMPOSE_FILE" ps
    echo ""
}

# Rollback function
rollback() {
    log_warning "========================================="
    log_warning "  INITIATING ROLLBACK"
    log_warning "========================================="

    # Restore database backup
    BACKUP_DIR="${PROJECT_ROOT}/backups"
    LATEST_BACKUP=$(cat "${BACKUP_DIR}/latest_backup.txt" 2>/dev/null || echo "")

    if [ -n "$LATEST_BACKUP" ] && [ -f "$LATEST_BACKUP" ]; then
        log_info "Restoring database from: $LATEST_BACKUP"
        bash "${PROJECT_ROOT}/deploy/database/restore-db.sh" production "$LATEST_BACKUP"
    else
        log_warning "No recent backup found, skipping database restore"
    fi

    # Get previous image tag
    PREVIOUS_TAG=$(docker images --format "{{.Tag}}" ${CI_REGISTRY_IMAGE}/backend | sed -n '2p')

    if [ -z "$PREVIOUS_TAG" ]; then
        log_error "No previous version found for rollback"
        exit 1
    fi

    log_info "Rolling back to tag: $PREVIOUS_TAG"

    export CI_COMMIT_TAG="$PREVIOUS_TAG"

    stop_containers
    start_containers
    wait_for_services
    disable_maintenance_mode

    log_success "Rollback completed"
}

# Send deployment notification
send_notification() {
    local status=$1
    local message=$2

    if [ -n "$SLACK_WEBHOOK_URL" ]; then
        curl -X POST "$SLACK_WEBHOOK_URL" \
            -H 'Content-Type: application/json' \
            -d "{\"text\":\"🚀 SIMVEB Production Deployment: $status\n$message\"}" \
            2>/dev/null || true
    fi
}

# Main deployment function
main() {
    local deployment_start=$(date +%s)

    echo ""
    log_critical "========================================="
    log_critical "  SIMVEB PRODUCTION DEPLOYMENT"
    log_critical "========================================="
    echo ""

    # Production safety check
    production_safety_check

    # Run deployment steps
    check_user
    check_prerequisites
    load_env
    docker_login
    backup_database

    # Notify deployment start
    send_notification "STARTED" "Deployment initiated at $(date)"

    enable_maintenance_mode
    pull_images
    stop_containers
    start_containers

    # Wait and verify services
    if ! wait_for_services; then
        log_error "Services failed to start properly"
        send_notification "FAILED" "Deployment failed - services did not start"
        rollback
        exit 1
    fi

    run_migrations
    optimize_application

    # Final health check
    if ! health_check; then
        log_error "Health check failed after deployment"
        send_notification "FAILED" "Deployment failed - health check failed"
        rollback
        exit 1
    fi

    disable_maintenance_mode
    show_status

    local deployment_end=$(date +%s)
    local deployment_duration=$((deployment_end - deployment_start))

    echo ""
    log_success "========================================="
    log_success "  DEPLOYMENT COMPLETED SUCCESSFULLY!"
    log_success "  Duration: ${deployment_duration}s"
    log_success "========================================="
    echo ""
    log_info "Production URLs:"
    log_info "  - Backend API:  https://api.simveb-bj.com"
    log_info "  - Portal:       https://simveb-bj.com"
    log_info "  - Backoffice:   https://admin.simveb-bj.com"
    log_info "  - Affiliate:    https://affiliate.simveb-bj.com"
    echo ""

    # Notify deployment success
    send_notification "SUCCESS" "Deployment completed successfully in ${deployment_duration}s"
}

# Handle script arguments
case "${1:-deploy}" in
    deploy)
        main
        ;;
    rollback)
        rollback
        ;;
    status)
        show_status
        ;;
    health)
        health_check
        ;;
    *)
        echo "Usage: $0 {deploy|rollback|status|health}"
        exit 1
        ;;
esac
