#!/bin/bash

# ========================================
# SIMVEB - Script d'Initialisation PostgreSQL
# Initialise les bases de données pour staging et production
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
    echo "Usage: $0 <environment>"
    echo "  environment: staging or production"
    exit 1
}

# Check arguments
if [ $# -ne 1 ]; then
    usage
fi

ENVIRONMENT=$1

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

log_info "========================================="
log_info "  PostgreSQL Database Initialization"
log_info "  Environment: $ENVIRONMENT"
log_info "========================================="
echo ""

export PGPASSWORD="$DB_PASSWORD"

# Test connection
log_info "Testing PostgreSQL connection..."
if ! psql -h "$DB_HOST" -U "$DB_USERNAME" -d postgres -c "SELECT version();" > /dev/null 2>&1; then
    log_error "Cannot connect to PostgreSQL server at $DB_HOST"
    exit 1
fi
log_success "PostgreSQL connection successful"

# Check if database exists
log_info "Checking if database exists..."
DB_EXISTS=$(psql -h "$DB_HOST" -U "$DB_USERNAME" -d postgres -t -c \
    "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME';" | xargs)

if [ "$DB_EXISTS" = "1" ]; then
    log_warning "Database $DB_NAME already exists"
    read -p "Do you want to drop and recreate it? (yes/no): " confirm

    if [ "$confirm" = "yes" ]; then
        log_info "Dropping database $DB_NAME..."
        dropdb -h "$DB_HOST" -U "$DB_USERNAME" "$DB_NAME"
        log_success "Database dropped"
    else
        log_info "Keeping existing database"
        exit 0
    fi
fi

# Create database
log_info "Creating database $DB_NAME..."
createdb -h "$DB_HOST" -U "$DB_USERNAME" "$DB_NAME"
log_success "Database created"

# Create extensions
log_info "Creating PostgreSQL extensions..."
psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" -c "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";"
psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" -c "CREATE EXTENSION IF NOT EXISTS \"pg_trgm\";"
psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" -c "CREATE EXTENSION IF NOT EXISTS \"unaccent\";"
log_success "Extensions created"

# Grant privileges
log_info "Granting privileges..."
psql -h "$DB_HOST" -U postgres -d "$DB_NAME" -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USERNAME;"
log_success "Privileges granted"

# Import initial schema if available
SCHEMA_FILE="/opt/simveb/simvebbase (1).sql"
if [ -f "$SCHEMA_FILE" ]; then
    log_info "Importing initial schema..."
    psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" < "$SCHEMA_FILE"
    log_success "Initial schema imported"
else
    log_warning "Initial schema file not found: $SCHEMA_FILE"
    log_info "You'll need to run migrations manually"
fi

# Show database info
log_info "Database information:"
psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_NAME" -c "\l+ $DB_NAME"

echo ""
log_success "========================================="
log_success "  Database initialization completed!"
log_success "========================================="
echo ""
log_info "Database: $DB_NAME"
log_info "Host: $DB_HOST"
log_info "User: $DB_USERNAME"
echo ""
log_info "Next steps:"
log_info "1. Run migrations: docker exec simveb-backend-${ENVIRONMENT} php artisan migrate"
log_info "2. Seed data: docker exec simveb-backend-${ENVIRONMENT} php artisan db:seed"
echo ""
