#!/bin/bash

# ========================================
# SIMVEB - Script de Restauration PostgreSQL
# Restaure la base de données depuis un backup
# ========================================

set -e

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
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

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Usage
usage() {
    echo "Usage: $0 <environment> <backup_file>"
    echo "  environment: staging or production"
    echo "  backup_file: path to backup file (.sql.gz or .sql)"
    exit 1
}

# Check arguments
if [ $# -ne 2 ]; then
    usage
fi

ENVIRONMENT=$1
BACKUP_FILE=$2

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    log_error "Backup file not found: $BACKUP_FILE"
    exit 1
fi

# Load environment file
ENV_FILE="/opt/simveb/.env.${ENVIRONMENT}"

if [ ! -f "$ENV_FILE" ]; then
    log_error "Environment file not found: $ENV_FILE"
    exit 1
fi

# Load DB credentials
source "$ENV_FILE"

# Get database name based on environment
if [ "$ENVIRONMENT" = "production" ]; then
    DB_NAME="simveb_production"
elif [ "$ENVIRONMENT" = "staging" ]; then
    DB_NAME="simveb_staging"
else
    log_error "Invalid environment: $ENVIRONMENT"
    exit 1
fi

# Safety confirmation for production
if [ "$ENVIRONMENT" = "production" ]; then
    log_warning "========================================="
    log_warning "  PRODUCTION DATABASE RESTORE"
    log_warning "========================================="
    log_warning "This will OVERWRITE the production database!"
    echo ""
    read -p "Are you absolutely sure? (type 'yes' to confirm): " confirm

    if [ "$confirm" != "yes" ]; then
        log_info "Restore cancelled by user"
        exit 0
    fi
fi

log_info "Starting database restore..."
log_info "Environment: $ENVIRONMENT"
log_info "Database: $DB_NAME"
log_info "Backup file: $BACKUP_FILE"

# Decompress if needed
TEMP_FILE=""
if [[ "$BACKUP_FILE" == *.gz ]]; then
    log_info "Decompressing backup file..."
    TEMP_FILE="/tmp/simveb_restore_$(date +%s).sql"
    gunzip -c "$BACKUP_FILE" > "$TEMP_FILE"
    RESTORE_FILE="$TEMP_FILE"
else
    RESTORE_FILE="$BACKUP_FILE"
fi

# Create a backup of current database before restore
log_info "Creating safety backup of current database..."
SAFETY_BACKUP="/opt/simveb/backups/pre_restore_${ENVIRONMENT}_$(date +%Y%m%d-%H%M%S).sql.gz"
bash "$(dirname "$0")/backup-db.sh" "$ENVIRONMENT" "${SAFETY_BACKUP%.gz}"

export PGPASSWORD="$DB_PASSWORD"

# Drop all connections to the database
log_info "Terminating active connections..."
psql -h "$DB_HOST" -U "$DB_USERNAME" -d postgres -c \
    "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '$DB_NAME' AND pid <> pg_backend_pid();" \
    2>/dev/null || true

# Drop and recreate database
log_warning "Dropping database $DB_NAME..."
dropdb -h "$DB_HOST" -U "$DB_USERNAME" "$DB_NAME" --if-exists

log_info "Creating database $DB_NAME..."
createdb -h "$DB_HOST" -U "$DB_USERNAME" "$DB_NAME"

# Restore database
log_info "Restoring database from backup..."
psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" < "$RESTORE_FILE"

# Clean up temp file
if [ -n "$TEMP_FILE" ] && [ -f "$TEMP_FILE" ]; then
    rm "$TEMP_FILE"
fi

log_success "Database restore completed successfully!"
log_info "Safety backup created at: $SAFETY_BACKUP"

# Verify restore
log_info "Verifying database..."
TABLE_COUNT=$(psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" -t -c \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';" | xargs)

log_success "Database verification complete. Found $TABLE_COUNT tables."
