#!/bin/bash

# ========================================
# SIMVEB - Script d'initialisation des bases de données
# Crée toutes les bases de données pour les microservices
# ========================================

set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    -- Kong Database
    CREATE DATABASE kong_db;
    GRANT ALL PRIVILEGES ON DATABASE kong_db TO $POSTGRES_USER;

    -- Auth Service Database
    CREATE DATABASE auth_db;
    GRANT ALL PRIVILEGES ON DATABASE auth_db TO $POSTGRES_USER;

    -- User Service Database
    CREATE DATABASE user_db;
    GRANT ALL PRIVILEGES ON DATABASE user_db TO $POSTGRES_USER;

    -- Vehicle Service Database
    CREATE DATABASE vehicle_db;
    GRANT ALL PRIVILEGES ON DATABASE vehicle_db TO $POSTGRES_USER;

    -- Immatriculation Service Database
    CREATE DATABASE immat_db;
    GRANT ALL PRIVILEGES ON DATABASE immat_db TO $POSTGRES_USER;

    -- Payment Service Database
    CREATE DATABASE payment_db;
    GRANT ALL PRIVILEGES ON DATABASE payment_db TO $POSTGRES_USER;

    -- Document Service Database
    CREATE DATABASE document_db;
    GRANT ALL PRIVILEGES ON DATABASE document_db TO $POSTGRES_USER;

    -- Notification Service Database
    CREATE DATABASE notification_db;
    GRANT ALL PRIVILEGES ON DATABASE notification_db TO $POSTGRES_USER;

    -- Integration Service Database
    CREATE DATABASE integration_db;
    GRANT ALL PRIVILEGES ON DATABASE integration_db TO $POSTGRES_USER;

    -- Config Service Database
    CREATE DATABASE config_db;
    GRANT ALL PRIVILEGES ON DATABASE config_db TO $POSTGRES_USER;
EOSQL

echo "✅ All databases created successfully!"
