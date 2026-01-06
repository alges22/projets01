# Guide Complet : API Gateway pour Microservices

## Table des Matières

1. [Qu'est-ce qu'un API Gateway ?](#quest-ce-quun-api-gateway)
2. [Kong API Gateway - Détails](#kong-api-gateway)
3. [Alternatives à Kong](#alternatives-à-kong)
4. [Comparatif des Solutions](#comparatif-des-solutions)
5. [Recommandations](#recommandations)
6. [Exemples de Configuration](#exemples-de-configuration)

---

## Qu'est-ce qu'un API Gateway ?

### Définition

Un **API Gateway** est un **point d'entrée unique** pour tous les clients (web, mobile, etc.) qui veulent accéder aux microservices.

### Schéma Simple

```
Sans API Gateway (Problématique):
┌─────────┐
│ Client  │
│ (Web)   │
└────┬────┘
     │
     ├──────────────┬──────────────┬──────────────┐
     │              │              │              │
┌────▼────┐   ┌────▼────┐   ┌────▼────┐   ┌────▼────┐
│Service 1│   │Service 2│   │Service 3│   │Service 4│
└─────────┘   └─────────┘   └─────────┘   └─────────┘

Problèmes:
- Client doit connaître toutes les URLs
- Pas de centralisation de la sécurité
- Gestion CORS complexe
- Pas de rate limiting centralisé


Avec API Gateway (Solution):
┌─────────┐
│ Client  │
│ (Web)   │
└────┬────┘
     │
     │ Une seule URL
     │
┌────▼────────────────┐
│   API GATEWAY       │
│  - Routing          │
│  - Authentification │
│  - Rate Limiting    │
│  - Load Balancing   │
│  - Monitoring       │
└────┬────────────────┘
     │
     ├──────────────┬──────────────┬──────────────┐
     │              │              │              │
┌────▼────┐   ┌────▼────┐   ┌────▼────┐   ┌────▼────┐
│Service 1│   │Service 2│   │Service 3│   │Service 4│
└─────────┘   └─────────┘   └─────────┘   └─────────┘

Avantages:
✅ Point d'entrée unique
✅ Sécurité centralisée
✅ Monitoring centralisé
✅ Gestion du trafic
```

### Fonctionnalités Principales

#### 1. **Routing (Routage)**

Rediriger les requêtes vers le bon service.

```
GET /api/users/*      → User Service
GET /api/vehicles/*   → Vehicle Service
GET /api/payments/*   → Payment Service
```

#### 2. **Authentication & Authorization**

Vérifier que l'utilisateur est authentifié avant d'accéder aux services.

```
Client → Gateway (vérifie JWT) → Service
```

#### 3. **Rate Limiting**

Limiter le nombre de requêtes par client.

```
100 requêtes/minute par IP
1000 requêtes/heure par utilisateur
```

#### 4. **Load Balancing**

Distribuer le trafic entre plusieurs instances d'un service.

```
Gateway → Service Instance 1
       → Service Instance 2
       → Service Instance 3
```

#### 5. **SSL Termination**

Gérer HTTPS au niveau du Gateway.

```
Client (HTTPS) → Gateway (HTTPS) → Services (HTTP)
```

#### 6. **CORS Handling**

Gérer les CORS pour tous les services.

#### 7. **Request/Response Transformation**

Modifier les requêtes/réponses.

```
- Ajouter headers
- Convertir formats (XML → JSON)
- Cacher données sensibles
```

#### 8. **Monitoring & Logging**

Centraliser les logs et métriques.

```
- Nombre de requêtes
- Temps de réponse
- Erreurs
- Trafic par service
```

#### 9. **Circuit Breaker**

Arrêter les appels vers un service en panne.

```
Service en panne → Gateway retourne erreur immédiate
                → Ne surcharge pas le service
```

#### 10. **Caching**

Cache les réponses pour améliorer les performances.

```
GET /api/vehicles/brands
→ Gateway retourne depuis cache (pas d'appel au service)
```

---

## Kong API Gateway

### Qu'est-ce que Kong ?

**Kong** est un **API Gateway open-source** très populaire, construit sur **Nginx** et **Lua**.

### Architecture Kong

```
┌─────────────────────────────────────────────┐
│              KONG GATEWAY                    │
├─────────────────────────────────────────────┤
│                                              │
│  ┌──────────────────────────────────────┐   │
│  │        Proxy Layer (Nginx)           │   │
│  │  - Recevoir requêtes                 │   │
│  │  - Router vers plugins               │   │
│  └──────────────┬───────────────────────┘   │
│                 │                            │
│  ┌──────────────▼───────────────────────┐   │
│  │           Plugins                     │   │
│  │  - Authentication (JWT, OAuth)       │   │
│  │  - Rate Limiting                     │   │
│  │  - CORS                              │   │
│  │  - Logging                           │   │
│  │  - Transformation                    │   │
│  │  - ... 50+ plugins                   │   │
│  └──────────────┬───────────────────────┘   │
│                 │                            │
│  ┌──────────────▼───────────────────────┐   │
│  │        Admin API                      │   │
│  │  - Configuration                      │   │
│  │  - Gestion Services/Routes           │   │
│  └──────────────────────────────────────┘   │
│                                              │
└──────────────────┬───────────────────────────┘
                   │
          ┌────────▼────────┐
          │   PostgreSQL    │  (Config storage)
          └─────────────────┘
```

### Fonctionnalités Kong

#### **1. Routing Avancé**

```yaml
services:
  - name: user-service
    url: http://user-service:8002
    routes:
      - name: users-route
        paths:
          - /api/users
        methods:
          - GET
          - POST
      - name: user-profile
        paths:
          - /api/users/profile
        methods:
          - GET
```

#### **2. Plugins (50+)**

**Authentication:**
- JWT
- OAuth 2.0
- API Key
- Basic Auth
- LDAP

**Security:**
- Rate Limiting
- IP Restriction
- Bot Detection
- CORS

**Traffic Control:**
- Request/Response Transformation
- Request Size Limiting
- Request Termination

**Analytics & Monitoring:**
- Prometheus
- Datadog
- Logging

**Exemple d'utilisation:**

```bash
# Ajouter JWT authentication
curl -X POST http://localhost:8001/services/user-service/plugins \
  --data "name=jwt"

# Ajouter rate limiting
curl -X POST http://localhost:8001/services/user-service/plugins \
  --data "name=rate-limiting" \
  --data "config.minute=100"

# Ajouter CORS
curl -X POST http://localhost:8001/services/user-service/plugins \
  --data "name=cors" \
  --data "config.origins=*"
```

#### **3. Admin API**

Kong expose une API REST pour la configuration:

```bash
# Lister les services
curl http://localhost:8001/services

# Ajouter un service
curl -X POST http://localhost:8001/services \
  --data "name=payment-service" \
  --data "url=http://payment-service:8005"

# Ajouter une route
curl -X POST http://localhost:8001/services/payment-service/routes \
  --data "paths[]=/api/payments"
```

#### **4. Kong Manager (GUI)**

Interface web pour gérer Kong (version Enterprise ou OSS avec plugin).

```
http://localhost:8002
```

#### **5. Performance**

Kong est **très performant** grâce à Nginx:
- **50,000+ requêtes/seconde** par instance
- Latence faible (~1ms overhead)

### Avantages Kong

✅ **Performance** - Basé sur Nginx (très rapide)
✅ **Plugins riches** - 50+ plugins officiels + communauté
✅ **Mature** - Utilisé par des milliers d'entreprises
✅ **Évolutif** - Scaling horizontal facile
✅ **Communauté** - Grande communauté active
✅ **Documentation** - Excellente documentation
✅ **Support** - Version Enterprise avec support
✅ **Monitoring** - Intégration Prometheus, Grafana, etc.

### Inconvénients Kong

❌ **Complexité** - Configuration peut être complexe
❌ **Dépendances** - Nécessite PostgreSQL ou Cassandra
❌ **Courbe d'apprentissage** - Temps d'apprentissage
❌ **Ressources** - Consomme plus de ressources
❌ **Debugging** - Lua peut être difficile à débuguer

---

## Alternatives à Kong

### 1. **Traefik** (Recommandé pour simplicité)

#### Qu'est-ce que Traefik ?

**Traefik** est un **reverse proxy et API Gateway moderne**, conçu pour être **simple** et **auto-configuré** avec Docker.

#### Architecture Traefik

```
┌────────────────────────────────────┐
│          TRAEFIK                    │
├────────────────────────────────────┤
│  - Auto-découverte (Docker labels) │
│  - Reverse Proxy                   │
│  - Load Balancing                  │
│  - SSL automatique (Let's Encrypt) │
│  - Dashboard                       │
└────────────────────────────────────┘
```

#### Configuration Traefik (Docker Labels)

```yaml
version: '3.8'

services:
  traefik:
    image: traefik:v2.10
    command:
      - "--api.dashboard=true"
      - "--providers.docker=true"
      - "--entrypoints.web.address=:80"
      - "--entrypoints.websecure.address=:443"
      - "--certificatesresolvers.letsencrypt.acme.email=admin@simveb.com"
      - "--certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json"
      - "--certificatesresolvers.letsencrypt.acme.httpchallenge.entrypoint=web"
    ports:
      - "80:80"
      - "443:443"
      - "8080:8080"  # Dashboard
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - ./letsencrypt:/letsencrypt

  # Service avec auto-configuration
  user-service:
    image: user-service:latest
    labels:
      # Activer Traefik
      - "traefik.enable=true"

      # Router HTTP
      - "traefik.http.routers.user.rule=PathPrefix(`/api/users`)"
      - "traefik.http.routers.user.entrypoints=web"

      # Redirection HTTPS
      - "traefik.http.middlewares.redirect-https.redirectscheme.scheme=https"
      - "traefik.http.routers.user.middlewares=redirect-https"

      # Router HTTPS
      - "traefik.http.routers.user-secure.rule=PathPrefix(`/api/users`)"
      - "traefik.http.routers.user-secure.entrypoints=websecure"
      - "traefik.http.routers.user-secure.tls.certresolver=letsencrypt"

      # Service
      - "traefik.http.services.user.loadbalancer.server.port=8002"
```

#### Avantages Traefik

✅ **Simplicité** - Configuration via Docker labels
✅ **Auto-découverte** - Détecte automatiquement les services
✅ **SSL automatique** - Let's Encrypt intégré
✅ **Dashboard** - Interface web incluse
✅ **Performance** - Très performant (Go)
✅ **Léger** - Peu de ressources
✅ **Moderne** - Conçu pour les conteneurs

#### Inconvénients Traefik

❌ **Plugins limités** - Moins de plugins que Kong
❌ **Authentication** - Pas d'auth avancée built-in
❌ **Rate limiting** - Basique comparé à Kong

---

### 2. **Nginx** (Solution basique)

#### Configuration Nginx

```nginx
# nginx.conf

upstream auth_service {
    server auth-service:8001;
}

upstream user_service {
    server user-service:8002;
}

upstream vehicle_service {
    server vehicle-service:8003;
}

server {
    listen 80;
    server_name api.simveb-bj.com;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api_limit:10m rate=10r/s;

    # Auth Service
    location /api/auth {
        limit_req zone=api_limit burst=20 nodelay;
        proxy_pass http://auth_service;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }

    # User Service (avec authentification)
    location /api/users {
        # Vérifier JWT (via auth_request)
        auth_request /auth/verify;

        proxy_pass http://user_service;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    # Vehicle Service
    location /api/vehicles {
        auth_request /auth/verify;
        proxy_pass http://vehicle_service;
    }

    # Auth verification endpoint
    location = /auth/verify {
        internal;
        proxy_pass http://auth_service/api/verify-token;
        proxy_pass_request_body off;
        proxy_set_header Content-Length "";
        proxy_set_header X-Original-URI $request_uri;
    }
}
```

#### Avantages Nginx

✅ **Simple** - Configuration facile
✅ **Performant** - Très rapide
✅ **Léger** - Peu de ressources
✅ **Bien connu** - Tout le monde connaît Nginx
✅ **Gratuit** - 100% open source

#### Inconvénients Nginx

❌ **Pas de plugins** - Tout à configurer manuellement
❌ **Pas de dashboard** - Configuration en fichiers
❌ **Auth basique** - Pas d'OAuth/JWT natif
❌ **Pas de discovery** - Configuration statique

---

### 3. **Envoy Proxy**

**Envoy** est un proxy moderne créé par Lyft, utilisé par Istio.

#### Configuration Envoy (YAML)

```yaml
# envoy.yaml
static_resources:
  listeners:
    - name: listener_0
      address:
        socket_address:
          address: 0.0.0.0
          port_value: 8000
      filter_chains:
        - filters:
            - name: envoy.filters.network.http_connection_manager
              typed_config:
                "@type": type.googleapis.com/envoy.extensions.filters.network.http_connection_manager.v3.HttpConnectionManager
                stat_prefix: ingress_http
                route_config:
                  name: local_route
                  virtual_hosts:
                    - name: backend
                      domains: ["*"]
                      routes:
                        # Auth Service
                        - match:
                            prefix: "/api/auth"
                          route:
                            cluster: auth_service

                        # User Service
                        - match:
                            prefix: "/api/users"
                          route:
                            cluster: user_service

                http_filters:
                  - name: envoy.filters.http.router

  clusters:
    - name: auth_service
      connect_timeout: 0.25s
      type: STRICT_DNS
      lb_policy: ROUND_ROBIN
      load_assignment:
        cluster_name: auth_service
        endpoints:
          - lb_endpoints:
              - endpoint:
                  address:
                    socket_address:
                      address: auth-service
                      port_value: 8001

    - name: user_service
      connect_timeout: 0.25s
      type: STRICT_DNS
      lb_policy: ROUND_ROBIN
      load_assignment:
        cluster_name: user_service
        endpoints:
          - lb_endpoints:
              - endpoint:
                  address:
                    socket_address:
                      address: user-service
                      port_value: 8002
```

#### Avantages Envoy

✅ **Moderne** - Conçu pour microservices
✅ **Observabilité** - Métriques excellentes
✅ **Service Mesh** - Compatible Istio
✅ **Performance** - Très performant (C++)
✅ **Protocoles** - HTTP/1.1, HTTP/2, gRPC

#### Inconvénients Envoy

❌ **Complexité** - Configuration YAML complexe
❌ **Courbe d'apprentissage** - Difficile à maîtriser
❌ **Pas de dashboard** - Nécessite outils externes

---

### 4. **API Gateway AWS / Azure / GCP**

Si vous êtes dans le cloud, utiliser les API Gateways managés.

#### AWS API Gateway

```yaml
Resources:
  ApiGateway:
    Type: AWS::ApiGatewayV2::Api
    Properties:
      Name: SIMVEB-API
      ProtocolType: HTTP

  UserServiceIntegration:
    Type: AWS::ApiGatewayV2::Integration
    Properties:
      ApiId: !Ref ApiGateway
      IntegrationType: HTTP_PROXY
      IntegrationUri: http://user-service:8002
      PayloadFormatVersion: '1.0'

  UserServiceRoute:
    Type: AWS::ApiGatewayV2::Route
    Properties:
      ApiId: !Ref ApiGateway
      RouteKey: 'GET /api/users'
      Target: !Join
        - /
        - - integrations
          - !Ref UserServiceIntegration
```

#### Avantages Cloud Gateway

✅ **Managé** - Pas de gestion infrastructure
✅ **Scalable** - Auto-scaling
✅ **Sécurisé** - WAF, DDoS protection
✅ **Intégrations** - Services cloud natifs

#### Inconvénients Cloud Gateway

❌ **Coût** - Peut être cher
❌ **Vendor Lock-in** - Dépendance au cloud
❌ **Latence** - Si services on-premise

---

### 5. **Spring Cloud Gateway** (Si Java)

Pour applications Java/Spring Boot.

```java
@Configuration
public class GatewayConfig {

    @Bean
    public RouteLocator customRouteLocator(RouteLocatorBuilder builder) {
        return builder.routes()
            // Auth Service
            .route("auth_route", r -> r
                .path("/api/auth/**")
                .uri("http://auth-service:8001"))

            // User Service avec rate limiting
            .route("user_route", r -> r
                .path("/api/users/**")
                .filters(f -> f
                    .requestRateLimiter(c -> c
                        .setRateLimiter(redisRateLimiter())))
                .uri("http://user-service:8002"))

            .build();
    }
}
```

#### Avantages Spring Cloud Gateway

✅ **Java ecosystem** - Si déjà Spring Boot
✅ **Intégration** - Spring Cloud (Eureka, Config)
✅ **Reactive** - WebFlux (performances)

#### Inconvénients Spring Cloud Gateway

❌ **JVM** - Nécessite Java
❌ **Ressources** - Consomme plus de RAM
❌ **Complexité** - Stack Spring

---

### 6. **KrakenD** (Ultra performant)

API Gateway open-source très performant.

```json
{
  "version": 3,
  "endpoints": [
    {
      "endpoint": "/api/users",
      "method": "GET",
      "backend": [
        {
          "url_pattern": "/users",
          "host": ["http://user-service:8002"]
        }
      ],
      "extra_config": {
        "qos/ratelimit/router": {
          "max_rate": 100
        }
      }
    },
    {
      "endpoint": "/api/vehicles",
      "method": "GET",
      "backend": [
        {
          "url_pattern": "/vehicles",
          "host": ["http://vehicle-service:8003"]
        }
      ]
    }
  ]
}
```

#### Avantages KrakenD

✅ **Ultra rapide** - Golang
✅ **Sans état** - Pas de base de données
✅ **Configuration** - Fichier JSON simple
✅ **Performance** - 70,000+ req/s

#### Inconvénients KrakenD

❌ **Communauté** - Plus petite que Kong
❌ **Plugins** - Moins de plugins
❌ **Documentation** - Moins complète

---

## Comparatif des Solutions

| Critère | Kong | Traefik | Nginx | Envoy | KrakenD |
|---------|------|---------|-------|-------|---------|
| **Performance** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Simplicité** | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Plugins** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| **Dashboard** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Auth** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| **Rate Limiting** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **SSL** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Communauté** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Documentation** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Ressources** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |

### Comparatif Fonctionnalités

| Fonctionnalité | Kong | Traefik | Nginx | Envoy | KrakenD |
|----------------|------|---------|-------|-------|---------|
| Routing | ✅ | ✅ | ✅ | ✅ | ✅ |
| Load Balancing | ✅ | ✅ | ✅ | ✅ | ✅ |
| JWT Auth | ✅ | ⚠️ | ⚠️ | ✅ | ✅ |
| OAuth 2.0 | ✅ | ❌ | ❌ | ⚠️ | ⚠️ |
| Rate Limiting | ✅ | ✅ | ✅ | ✅ | ✅ |
| CORS | ✅ | ✅ | ✅ | ✅ | ✅ |
| Circuit Breaker | ✅ | ❌ | ❌ | ✅ | ✅ |
| Request Transform | ✅ | ⚠️ | ⚠️ | ✅ | ✅ |
| Caching | ✅ | ⚠️ | ✅ | ✅ | ✅ |
| Metrics | ✅ | ✅ | ⚠️ | ✅ | ✅ |
| Service Discovery | ✅ | ✅ | ❌ | ✅ | ❌ |
| Health Checks | ✅ | ✅ | ✅ | ✅ | ✅ |
| gRPC | ✅ | ✅ | ✅ | ✅ | ✅ |
| WebSocket | ✅ | ✅ | ✅ | ✅ | ✅ |

Légende:
- ✅ = Support natif
- ⚠️ = Support via config/plugin
- ❌ = Pas de support

---

## Recommandations

### Pour SIMVEB, je recommande selon le contexte:

#### **Option 1: Traefik** (⭐ Recommandé pour démarrer)

**Utilisez si:**
- ✅ Vous voulez de la **simplicité**
- ✅ Vous utilisez **Docker**
- ✅ Vous avez besoin de **SSL automatique**
- ✅ Vous voulez **démarrer rapidement**
- ✅ Équipe **pas familière** avec API Gateways

**Configuration minimale:**

```yaml
# Simple et efficace
services:
  traefik:
    image: traefik:v2.10
    command:
      - "--providers.docker=true"
      - "--entrypoints.web.address=:80"
    ports:
      - "80:80"
      - "8080:8080"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock

  user-service:
    image: user-service
    labels:
      - "traefik.http.routers.user.rule=PathPrefix(`/api/users`)"
```

#### **Option 2: Kong** (⭐⭐ Recommandé pour production avancée)

**Utilisez si:**
- ✅ Vous avez besoin de **fonctionnalités avancées**
- ✅ **Authentification complexe** (OAuth, JWT avancé)
- ✅ **Rate limiting sophistiqué**
- ✅ **Plugins nombreux**
- ✅ Équipe prête à **investir du temps**

**Quand passer de Traefik à Kong:**
- Besoin d'OAuth 2.0 complet
- Rate limiting avancé (par user, par API key, etc.)
- Transformation de requêtes complexe
- Analytics détaillés

#### **Option 3: Nginx** (⭐ Pour budget/ressources limités)

**Utilisez si:**
- ✅ **Ressources limitées**
- ✅ Besoin **basique** seulement
- ✅ Équipe connaît **déjà Nginx**
- ✅ Configuration **simple**

#### **Option 4: KrakenD** (Pour performance ultime)

**Utilisez si:**
- ✅ **Performance critique**
- ✅ Trafic **très élevé**
- ✅ Configuration **stateless** préférée

---

## Recommandation Finale pour SIMVEB

### Phase 1: **Traefik** (0-6 mois)

```
Démarrer avec Traefik car:
✅ Simple à configurer
✅ Auto-découverte Docker
✅ SSL automatique
✅ Suffit pour démarrer les microservices
```

### Phase 2: **Migrer vers Kong** (6-12 mois) - Si nécessaire

```
Migrer vers Kong quand:
- Besoin OAuth 2.0 complet
- Rate limiting avancé requis
- Plugins tiers nécessaires
- Analytics détaillés demandés
```

---

## Exemples de Configuration

### Configuration Complète Traefik pour SIMVEB

Voir fichier: `microservices/api-gateway/traefik/docker-compose.traefik.yml`

### Configuration Complète Nginx pour SIMVEB

Voir fichier: `microservices/api-gateway/nginx/nginx.conf`

---

**Version:** 1.0
**Date:** 2026-01-03
**Recommandation:** Traefik pour démarrer, Kong pour production avancée
