#!/bin/bash

# ========================================
# SIMVEB - Script de Backup PostgreSQL
# Sauvegarde la base de données
# ========================================

set -e

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Usage
usage() {
    echo "Usage: $0 <environment> [backup_file]"
    echo "  environment: staging or production"
    echo "  backup_file: optional, path to backup file (default: auto-generated)"
    exit 1
}

# Check arguments
if [ $# -lt 1 ]; then
    usage
fi

ENVIRONMENT=$1
BACKUP_FILE=${2:-""}

# Load environment file
ENV_FILE="/opt/simveb/.env.${ENVIRONMENT}"

if [ ! -f "$ENV_FILE" ]; then
    log_error "Environment file not found: $ENV_FILE"
    exit 1
fi

# Load DB credentials
source "$ENV_FILE"

# Set default backup file if not provided
if [ -z "$BACKUP_FILE" ]; then
    BACKUP_DIR="/opt/simveb/backups"
    mkdir -p "$BACKUP_DIR"
    TIMESTAMP=$(date +%Y%m%d-%H%M%S)
    BACKUP_FILE="${BACKUP_DIR}/simveb_${ENVIRONMENT}_${TIMESTAMP}.sql"
fi

# Get database name based on environment
if [ "$ENVIRONMENT" = "production" ]; then
    DB_NAME="simveb_production"
elif [ "$ENVIRONMENT" = "staging" ]; then
    DB_NAME="simveb_staging"
else
    log_error "Invalid environment: $ENVIRONMENT"
    exit 1
fi

log_info "Starting database backup..."
log_info "Environment: $ENVIRONMENT"
log_info "Database: $DB_NAME"
log_info "Backup file: $BACKUP_FILE"

# Create backup
export PGPASSWORD="$DB_PASSWORD"

pg_dump \
    -h "$DB_HOST" \
    -p 5432 \
    -U "$DB_USERNAME" \
    -d "$DB_NAME" \
    --verbose \
    --format=plain \
    --no-owner \
    --no-acl \
    > "$BACKUP_FILE" 2>&1

# Compress backup
if [ -f "$BACKUP_FILE" ]; then
    log_info "Compressing backup..."
    gzip -f "$BACKUP_FILE"
    BACKUP_FILE="${BACKUP_FILE}.gz"

    # Get file size
    SIZE=$(du -h "$BACKUP_FILE" | cut -f1)

    log_success "Backup completed successfully!"
    log_info "File: $BACKUP_FILE"
    log_info "Size: $SIZE"

    # Create a symbolic link to latest backup
    LATEST_LINK="/opt/simveb/backups/latest_${ENVIRONMENT}.sql.gz"
    ln -sf "$BACKUP_FILE" "$LATEST_LINK"

    # Clean old backups (keep last 30)
    log_info "Cleaning old backups (keeping last 30)..."
    cd "/opt/simveb/backups"
    ls -t simveb_${ENVIRONMENT}_*.sql.gz 2>/dev/null | tail -n +31 | xargs -r rm --

    log_success "Backup process completed!"
else
    log_error "Backup file was not created!"
    exit 1
fi
