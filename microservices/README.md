# SIMVEB - Architecture Microservices

Architecture microservices simple et pragmatique pour SIMVEB (Système d'Immatriculation des Véhicules du Bénin).

## 📚 Documentation

- **[MICROSERVICES_ARCHITECTURE.md](MICROSERVICES_ARCHITECTURE.md)** - Documentation complète de l'architecture
- **[docker-compose.microservices.yml](docker-compose.microservices.yml)** - Configuration Docker Compose

## 🏗️ Architecture

### Microservices

1. **Auth Service** (Port 8001) - Authentification et gestion des tokens
2. **User Service** (Port 8002) - Gestion des utilisateurs et profils
3. **Vehicle Service** (Port 8003) - Gestion des véhicules et catalogue
4. **Immatriculation Service** (Port 8004) - Processus d'immatriculation
5. **Payment Service** (Port 8005) - Paiements et facturation
6. **Document Service** (Port 8006) - Gestion des documents
7. **Notification Service** (Port 8007) - Emails, SMS, Push
8. **Integration Service** (Port 8008) - Intégrations externes (ANIP, DGI)
9. **Config Service** (Port 8009) - Configuration centralisée

### Infrastructure

- **API Gateway** (Kong) - Port 8000
- **PostgreSQL** - Port 5432
- **Redis** - Port 6379
- **RabbitMQ** - Port 5672 (Management: 15672)

## 🚀 Démarrage Rapide

### Prérequis

- Docker 20+
- Docker Compose 2+
- 8 GB RAM minimum
- 20 GB disk

### Installation

```bash
# 1. Configurer les variables d'environnement
cp .env.example .env
nano .env

# 2. Créer les services (à implémenter)
# mkdir -p services/{auth,user,vehicle,immat,payment,document,notification,integration,config}-service

# 3. Démarrer l'infrastructure
docker-compose -f docker-compose.microservices.yml up -d postgres redis rabbitmq

# 4. Démarrer Kong
docker-compose -f docker-compose.microservices.yml up -d kong-migration api-gateway

# 5. Démarrer les microservices
docker-compose -f docker-compose.microservices.yml up -d
```

### Vérification

```bash
# Vérifier que tous les services sont démarrés
docker-compose -f docker-compose.microservices.yml ps

# Vérifier les logs
docker-compose -f docker-compose.microservices.yml logs -f

# Tester l'API Gateway
curl http://localhost:8000

# Accéder au Kong Manager
open http://localhost:8002

# Accéder au RabbitMQ Management
open http://localhost:15672  # Login: simveb / password
```

## 📊 Structure des Services

### Structure Recommandée pour un Microservice

```
service-name/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/          # Business logic
│   ├── Repositories/      # Data access
│   └── Events/            # Domain events
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
├── Dockerfile
├── composer.json
└── .env.example
```

### Dockerfile Type pour un Service

```dockerfile
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    postgresql-dev \
    curl \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 8001

# Start server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001"]
```

## 🔧 Configuration Kong (API Gateway)

### Ajouter un Service

```bash
# Créer le service
curl -i -X POST http://localhost:8001/services \
  --data "name=auth-service" \
  --data "url=http://auth-service:8001"

# Créer la route
curl -i -X POST http://localhost:8001/services/auth-service/routes \
  --data "paths[]=/api/auth" \
  --data "strip_path=false"

# Ajouter rate limiting
curl -i -X POST http://localhost:8001/services/auth-service/plugins \
  --data "name=rate-limiting" \
  --data "config.minute=100" \
  --data "config.policy=local"

# Ajouter JWT authentication
curl -i -X POST http://localhost:8001/services/auth-service/plugins \
  --data "name=jwt"
```

### Configuration Déclarative Kong

```yaml
# kong.yml
_format_version: "3.0"

services:
  - name: auth-service
    url: http://auth-service:8001
    routes:
      - name: auth-route
        paths:
          - /api/auth
        strip_path: false
    plugins:
      - name: rate-limiting
        config:
          minute: 100

  - name: user-service
    url: http://user-service:8002
    routes:
      - name: user-route
        paths:
          - /api/users
        strip_path: false
    plugins:
      - name: jwt
      - name: rate-limiting
        config:
          minute: 200

  - name: vehicle-service
    url: http://vehicle-service:8003
    routes:
      - name: vehicle-route
        paths:
          - /api/vehicles
        strip_path: false
    plugins:
      - name: jwt
```

## 🔄 Communication entre Services

### HTTP REST (Synchrone)

```php
// Dans User Service, appeler Auth Service
use Illuminate\Support\Facades\Http;

$response = Http::withToken($serviceToken)
    ->get('http://auth-service:8001/api/verify-token');

if ($response->successful()) {
    $tokenData = $response->json();
}
```

### Events (Asynchrone via RabbitMQ)

```php
// Publisher (Immat Service)
use App\Events\ImmatriculationCreated;

event(new ImmatriculationCreated($immatriculation));

// Listener (Notification Service)
class SendImmatriculationNotification
{
    public function handle(ImmatriculationCreated $event)
    {
        // Envoyer notification
        Mail::to($event->immatriculation->user->email)
            ->send(new ImmatriculationCreatedMail($event->immatriculation));
    }
}
```

## 📈 Monitoring

Le monitoring est déjà configuré via Prometheus + Grafana (voir `/monitoring`).

### Métriques par Service

Chaque microservice expose des métriques sur `/metrics`:

```
http://auth-service:8001/metrics
http://user-service:8002/metrics
http://vehicle-service:8003/metrics
...
```

### Health Checks

Chaque service expose un endpoint `/health`:

```bash
curl http://localhost:8001/health  # Auth Service
curl http://localhost:8002/health  # User Service
```

## 🧪 Tests

### Tests Unitaires

```bash
# Dans chaque service
cd services/auth-service
composer test
```

### Tests d'Intégration

```bash
# Tester la communication entre services
cd tests/integration
./run-integration-tests.sh
```

## 📋 Roadmap de Migration

### Phase 1: Infrastructure (2 semaines)

- [x] Docker Compose configuration
- [x] API Gateway (Kong)
- [x] Message Broker (RabbitMQ)
- [ ] Service Discovery (optionnel)

### Phase 2: Premier Service (3 semaines)

- [ ] Extraire Auth Service du monolithe
- [ ] Migrer la base de données
- [ ] Configurer Kong routing
- [ ] Tests d'intégration

### Phase 3: Services Critiques (6 semaines)

- [ ] User Service
- [ ] Payment Service
- [ ] Notification Service

### Phase 4: Services Métier (8 semaines)

- [ ] Vehicle Service
- [ ] Immatriculation Service
- [ ] Document Service

### Phase 5: Services Support (4 semaines)

- [ ] Integration Service
- [ ] Config Service

### Phase 6: Décommissionnement (2 semaines)

- [ ] Migration complète
- [ ] Désactivation monolithe
- [ ] Nettoyage

**Total: ~25 semaines (~6 mois)**

## 🚨 Troubleshooting

### Services ne démarrent pas

```bash
# Vérifier les logs
docker-compose -f docker-compose.microservices.yml logs service-name

# Vérifier la santé
docker-compose -f docker-compose.microservices.yml ps
```

### Kong ne route pas correctement

```bash
# Vérifier la configuration Kong
curl http://localhost:8001/services
curl http://localhost:8001/routes

# Recharger la configuration
curl -i -X POST http://localhost:8001/config
```

### Problèmes de base de données

```bash
# Vérifier PostgreSQL
docker exec -it simveb-postgres psql -U simveb -c "\l"

# Recréer les bases
docker-compose -f docker-compose.microservices.yml down -v
docker-compose -f docker-compose.microservices.yml up -d postgres
```

## 📚 Ressources

- **Kong Documentation**: https://docs.konghq.com/
- **RabbitMQ Documentation**: https://www.rabbitmq.com/documentation.html
- **Microservices Patterns**: https://microservices.io/patterns/
- **Laravel Documentation**: https://laravel.com/docs

## 🤝 Contribution

1. Créer une branche pour le service
2. Implémenter le service
3. Ajouter les tests
4. Créer une Pull Request

## 📄 License

Projet SIMVEB - Gouvernement du Bénin

---

**Version:** 1.0
**Date:** 2026-01-03
**Architecture:** Microservices Simple
