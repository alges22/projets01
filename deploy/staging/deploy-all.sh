#!/bin/bash

# ========================================
# SIMVEB - Script de Déploiement STAGING
# Déploie tous les composants sur staging
# ========================================

set -e  # Exit on error

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="/opt/simveb"
DEPLOY_USER="simveb"
DOCKER_COMPOSE_FILE="${PROJECT_ROOT}/deploy/staging/docker-compose.yml"
ENV_FILE="${PROJECT_ROOT}/.env.staging"

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

# Check if running as deploy user
check_user() {
    if [ "$(whoami)" != "$DEPLOY_USER" ]; then
        log_error "This script must be run as $DEPLOY_USER user"
        exit 1
    fi
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

# Backup database
backup_database() {
    log_info "Creating database backup..."

    BACKUP_DIR="${PROJECT_ROOT}/backups"
    TIMESTAMP=$(date +%Y%m%d-%H%M%S)
    BACKUP_FILE="${BACKUP_DIR}/simveb_staging_${TIMESTAMP}.sql"

    mkdir -p "$BACKUP_DIR"

    # Run backup script
    if [ -f "${PROJECT_ROOT}/deploy/database/backup-db.sh" ]; then
        bash "${PROJECT_ROOT}/deploy/database/backup-db.sh" staging "$BACKUP_FILE"
        log_success "Database backup created: $BACKUP_FILE"
    else
        log_warning "Backup script not found, skipping backup"
    fi
}

# Stop old containers
stop_containers() {
    log_info "Stopping old containers..."

    cd "$PROJECT_ROOT"
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

# Run migrations
run_migrations() {
    log_info "Running database migrations..."

    # Wait for backend to be ready
    sleep 10

    docker exec simveb-backend-staging php artisan migrate --force

    log_success "Migrations completed"
}

# Clear and warm cache
optimize_application() {
    log_info "Optimizing application..."

    docker exec simveb-backend-staging php artisan config:cache
    docker exec simveb-backend-staging php artisan route:cache
    docker exec simveb-backend-staging php artisan view:cache
    docker exec simveb-backend-staging php artisan optimize

    log_success "Application optimized"
}

# Health check
health_check() {
    log_info "Running health checks..."

    # Wait for services to be ready
    sleep 15

    # Check backend
    if curl -f -s http://localhost:8080/health > /dev/null; then
        log_success "Backend is healthy"
    else
        log_error "Backend health check failed"
        exit 1
    fi

    # Check portal
    if curl -f -s http://localhost:3000 > /dev/null; then
        log_success "Portal is healthy"
    else
        log_warning "Portal health check failed"
    fi

    # Check backoffice
    if curl -f -s http://localhost:3001 > /dev/null; then
        log_success "Backoffice is healthy"
    else
        log_warning "Backoffice health check failed"
    fi

    # Check affiliate
    if curl -f -s http://localhost:3002 > /dev/null; then
        log_success "Affiliate is healthy"
    else
        log_warning "Affiliate health check failed"
    fi
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
    log_warning "Rolling back to previous version..."

    # Get previous image tag
    PREVIOUS_TAG=$(docker images --format "{{.Tag}}" ${CI_REGISTRY_IMAGE}/backend | sed -n '2p')

    if [ -z "$PREVIOUS_TAG" ]; then
        log_error "No previous version found for rollback"
        exit 1
    fi

    log_info "Rolling back to tag: $PREVIOUS_TAG"

    export CI_COMMIT_SHORT_SHA="$PREVIOUS_TAG"

    stop_containers
    start_containers

    log_success "Rollback completed"
}

# Main deployment function
main() {
    echo ""
    log_info "========================================="
    log_info "  SIMVEB STAGING DEPLOYMENT"
    log_info "========================================="
    echo ""

    # Run deployment steps
    check_user
    check_prerequisites
    load_env
    docker_login
    backup_database
    pull_images
    stop_containers
    start_containers

    # Wait for containers to be ready
    sleep 20

    run_migrations
    optimize_application
    health_check
    show_status

    echo ""
    log_success "========================================="
    log_success "  DEPLOYMENT COMPLETED SUCCESSFULLY!"
    log_success "========================================="
    echo ""
    log_info "Staging URLs:"
    log_info "  - Backend API:  http://localhost:8080"
    log_info "  - Portal:       http://localhost:3000"
    log_info "  - Backoffice:   http://localhost:3001"
    log_info "  - Affiliate:    http://localhost:3002"
    echo ""
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
