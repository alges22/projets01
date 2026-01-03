# Guide de Monitoring SIMVEB

## Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture de Monitoring](#architecture-de-monitoring)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Dashboards Grafana](#dashboards-grafana)
6. [Alertes](#alertes)
7. [Logs avec Loki](#logs-avec-loki)
8. [Métriques Collectées](#métriques-collectées)
9. [Troubleshooting](#troubleshooting)

---

## Vue d'ensemble

### Stack de Monitoring

Le système de monitoring SIMVEB est composé de :

- **Prometheus** : Collecte et stockage des métriques
- **Grafana** : Visualisation et dashboards
- **Loki** : Agrégation de logs
- **Promtail** : Agent de collecte de logs
- **Alertmanager** : Gestion des alertes et notifications
- **Node Exporter** : Métriques système (CPU, RAM, Disk)
- **cAdvisor** : Métriques Docker
- **PostgreSQL Exporter** : Métriques base de données
- **Redis Exporter** : Métriques cache
- **Blackbox Exporter** : Monitoring HTTP/HTTPS

### Métriques Surveillées

```
┌─────────────────────────────────────────┐
│         INFRASTRUCTURE                   │
├─────────────────────────────────────────┤
│ ✅ CPU, RAM, Disk (VMs)                 │
│ ✅ Network I/O                           │
│ ✅ Conteneurs Docker                     │
│ ✅ Services système                      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│         APPLICATIONS                     │
├─────────────────────────────────────────┤
│ ✅ Backend API (Laravel)                │
│ ✅ Portal (Nuxt)                        │
│ ✅ Backoffice (Vue)                     │
│ ✅ Affiliate (Vue)                      │
│ ✅ Disponibilité HTTP/HTTPS             │
│ ✅ Temps de réponse                     │
│ ✅ Codes d'erreur                       │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│         BASES DE DONNÉES                 │
├─────────────────────────────────────────┤
│ ✅ PostgreSQL (connexions, queries)     │
│ ✅ Redis (mémoire, clients)             │
│ ✅ Slow queries                          │
│ ✅ Replication lag                       │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│         SÉCURITÉ                         │
├─────────────────────────────────────────┤
│ ✅ Certificats SSL (expiration)         │
│ ✅ Fail2Ban (bannissements)             │
│ ✅ Logs d'authentification              │
│ ✅ Tentatives d'intrusion               │
└─────────────────────────────────────────┘
```

---

## Architecture de Monitoring

### Schéma Global

```
┌──────────────────────────────────────────────────────┐
│            VM MONITORING (10.x.x.50)                 │
│                                                       │
│  ┌────────────┐  ┌────────────┐  ┌───────────────┐ │
│  │ Prometheus │  │  Grafana   │  │ Alertmanager  │ │
│  └─────┬──────┘  └──────┬─────┘  └───────┬───────┘ │
│        │                │                  │         │
│        │  ┌─────────────┘                  │         │
│        │  │                                │         │
│  ┌─────▼──▼──┐  ┌─────────────┐  ┌───────▼───────┐ │
│  │   Loki    │  │  Promtail   │  │     Slack     │ │
│  └───────────┘  └─────────────┘  │    Email      │ │
│                                   └───────────────┘ │
└──────────────────┬───────────────────────────────────┘
                   │
        ┌──────────┼──────────┬──────────┐
        │          │          │          │
        │          │          │          │
    ┌───▼───┐  ┌──▼────┐ ┌──▼────┐ ┌──▼────┐
    │VM App │  │VM App │ │VM DB  │ │VM DB  │
    │Staging│  │ Prod  │ │Staging│ │ Prod  │
    └───────┘  └───────┘ └───────┘ └───────┘

    Sur chaque VM:
    - Node Exporter (9100)
    - cAdvisor (8080) - VMs App
    - PostgreSQL Exporter (9187) - VMs DB
    - Promtail (collecte logs)
```

---

## Installation

### 1. Préparer le Serveur de Monitoring

```bash
# VM Monitoring dédiée (ou VM App si ressources suffisantes)
# Recommandations: 2 CPU, 4GB RAM minimum

# Installation Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# Cloner le repo ou récupérer les fichiers monitoring/
cd /opt
sudo mkdir simveb-monitoring
cd simveb-monitoring
```

### 2. Copier les Fichiers de Configuration

```bash
# Structure requise
simveb-monitoring/
├── docker-compose.yml
├── prometheus/
│   ├── prometheus.yml
│   ├── alerts/
│   │   └── simveb_alerts.yml
│   └── blackbox.yml
├── grafana/
│   ├── provisioning/
│   └── dashboards/
├── loki/
│   ├── loki-config.yml
│   └── promtail-config.yml
└── alertmanager/
    └── config.yml
```

### 3. Configurer les Variables d'Environnement

```bash
# Créer le fichier .env
cat > .env << 'EOF'
# Grafana
GRAFANA_ADMIN_PASSWORD=VotreMotDePasseSecurise

# SMTP pour notifications email
SMTP_USER=votre-email@gmail.com
SMTP_PASSWORD=VotreMotDePasseAppGmail

# Slack (optionnel)
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL

# Base de données
DB_HOST_STAGING=10.x.x.20
DB_HOST_PROD=10.x.x.40
DB_PASSWORD_STAGING=password_staging
DB_PASSWORD_PROD=password_production

# Redis
REDIS_PASSWORD=redis_password
EOF

chmod 600 .env
```

### 4. Mettre à Jour les IPs dans prometheus.yml

Éditer `prometheus/prometheus.yml` et remplacer les `10.x.x.XX` par vos vraies IPs.

### 5. Déployer le Stack de Monitoring

```bash
# Démarrer tous les services
docker-compose up -d

# Vérifier que tout est démarré
docker-compose ps

# Voir les logs
docker-compose logs -f
```

### 6. Installer les Exporters sur les VMs

#### Sur chaque VM App (Staging et Production)

```bash
# Node Exporter
docker run -d \
  --name=node-exporter \
  --restart=unless-stopped \
  --net="host" \
  --pid="host" \
  -v "/:/host:ro,rslave" \
  prom/node-exporter:latest \
  --path.rootfs=/host

# cAdvisor
docker run -d \
  --name=cadvisor \
  --restart=unless-stopped \
  --privileged \
  --volume=/:/rootfs:ro \
  --volume=/var/run:/var/run:ro \
  --volume=/sys:/sys:ro \
  --volume=/var/lib/docker/:/var/lib/docker:ro \
  --publish=8080:8080 \
  gcr.io/cadvisor/cadvisor:latest

# Redis Exporter (si Redis sur cette VM)
docker run -d \
  --name=redis-exporter \
  --restart=unless-stopped \
  -e REDIS_ADDR="localhost:6379" \
  -e REDIS_PASSWORD="your_redis_password" \
  --publish=9121:9121 \
  oliver006/redis_exporter:latest

# Promtail pour les logs
docker run -d \
  --name=promtail \
  --restart=unless-stopped \
  --volume=/var/log:/var/log:ro \
  --volume=/var/lib/docker/containers:/var/lib/docker/containers:ro \
  --volume=/opt/simveb-monitoring/loki/promtail-config.yml:/etc/promtail/config.yml:ro \
  --publish=9080:9080 \
  grafana/promtail:latest \
  -config.file=/etc/promtail/config.yml
```

#### Sur chaque VM DB (Staging et Production)

```bash
# Node Exporter
docker run -d \
  --name=node-exporter \
  --restart=unless-stopped \
  --net="host" \
  --pid="host" \
  -v "/:/host:ro,rslave" \
  prom/node-exporter:latest \
  --path.rootfs=/host

# PostgreSQL Exporter
docker run -d \
  --name=postgres-exporter \
  --restart=unless-stopped \
  -e DATA_SOURCE_NAME="postgresql://simveb:password@localhost:5432/simveb_staging?sslmode=require" \
  --publish=9187:9187 \
  prometheuscommunity/postgres-exporter:latest

# Promtail
docker run -d \
  --name=promtail \
  --restart=unless-stopped \
  --volume=/var/log:/var/log:ro \
  --volume=/opt/simveb-monitoring/loki/promtail-config.yml:/etc/promtail/config.yml:ro \
  --publish=9080:9080 \
  grafana/promtail:latest \
  -config.file=/etc/promtail/config.yml
```

### 7. Configurer le Firewall

```bash
# Sur VM Monitoring - Autoriser accès depuis IPs admin
sudo ufw allow from VOTRE_IP to any port 3000 comment 'Grafana'
sudo ufw allow from VOTRE_IP to any port 9090 comment 'Prometheus'

# Sur VMs App/DB - Autoriser Prometheus
sudo ufw allow from 10.x.x.50 to any port 9100 comment 'Node Exporter'
sudo ufw allow from 10.x.x.50 to any port 8080 comment 'cAdvisor'
sudo ufw allow from 10.x.x.50 to any port 9187 comment 'PostgreSQL Exporter'
sudo ufw allow from 10.x.x.50 to any port 9121 comment 'Redis Exporter'
```

---

## Configuration

### Accès aux Interfaces

| Service | URL | Credentials |
|---------|-----|-------------|
| **Grafana** | http://monitoring-vm:3000 | admin / (voir .env) |
| **Prometheus** | http://monitoring-vm:9090 | - |
| **Alertmanager** | http://monitoring-vm:9093 | - |

### Configuration Grafana

#### 1. Premier Login

1. Accéder à `http://monitoring-vm:3000`
2. Login: `admin` / Mot de passe depuis `.env`
3. Changer le mot de passe

#### 2. Ajouter les Data Sources

**Prometheus:**
```
Configuration > Data Sources > Add data source
Type: Prometheus
URL: http://prometheus:9090
Save & Test
```

**Loki:**
```
Configuration > Data Sources > Add data source
Type: Loki
URL: http://loki:3100
Save & Test
```

#### 3. Importer les Dashboards

Dashboards recommandés (à importer via ID):

- **Node Exporter Full**: ID `1860`
- **Docker Container & Host Metrics**: ID `10619`
- **PostgreSQL Database**: ID `9628`
- **Redis**: ID `11835`
- **Nginx**: ID `12708`
- **Loki Logs**: ID `13639`

```
Dashboards > Import > Enter ID > Load > Select Prometheus > Import
```

#### 4. Créer des Dashboards Personnalisés

Exemple de panels utiles :

**Panel 1 - Disponibilité des Services:**
```promql
up{job=~"blackbox-http.*"}
```

**Panel 2 - CPU Usage:**
```promql
100 - (avg by (instance) (rate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)
```

**Panel 3 - Memory Usage:**
```promql
(node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) / node_memory_MemTotal_bytes * 100
```

**Panel 4 - Disk Usage:**
```promql
(node_filesystem_size_bytes{mountpoint="/"} - node_filesystem_free_bytes{mountpoint="/"}) / node_filesystem_size_bytes{mountpoint="/"} * 100
```

**Panel 5 - Requêtes PostgreSQL:**
```promql
rate(pg_stat_database_xact_commit[5m])
```

**Panel 6 - Redis Memory:**
```promql
redis_memory_used_bytes / redis_memory_max_bytes * 100
```

---

## Alertes

### Alertes Configurées

Voir `monitoring/prometheus/alerts/simveb_alerts.yml` pour la liste complète.

**Alertes Critiques:**
- VM Down
- Service Down
- PostgreSQL Down
- Disk Space < 10%
- Memory > 95%
- CPU > 95%
- SSL Certificate expiring < 7 days

**Alertes Warning:**
- High CPU (> 80%)
- High Memory (> 80%)
- Low Disk Space (< 20%)
- Slow queries
- High error rate
- SSL Certificate expiring < 30 days

### Tester les Alertes

```bash
# Simuler une VM down
docker stop node-exporter

# Vérifier dans Prometheus
# http://monitoring-vm:9090/alerts

# Vérifier dans Alertmanager
# http://monitoring-vm:9093
```

### Notifications

Les alertes sont envoyées via :
- **Email** (SMTP Gmail)
- **Slack** (Webhooks)

Configuration dans `alertmanager/config.yml`.

---

## Logs avec Loki

### Requêtes LogQL Utiles

**Logs d'erreur Laravel:**
```logql
{job="laravel"} |= "ERROR"
```

**Logs Nginx avec status 5xx:**
```logql
{job="nginx", type="access"} | json | status >= 500
```

**Tentatives SSH échouées:**
```logql
{job="auth"} |= "Failed password"
```

**Logs PostgreSQL slow queries:**
```logql
{job="postgresql"} | regexp "duration: (?P<duration>\\d+\\.\\d+) ms" | duration > 1000
```

**Fail2Ban bans:**
```logql
{job="fail2ban"} |= "Ban"
```

### Alertes sur Logs

Configurées dans Loki Ruler (voir `loki/loki-config.yml`).

---

## Métriques Collectées

### Node Exporter (Système)

- `node_cpu_seconds_total` - Usage CPU
- `node_memory_MemTotal_bytes`, `node_memory_MemAvailable_bytes` - Mémoire
- `node_filesystem_size_bytes`, `node_filesystem_free_bytes` - Disque
- `node_network_receive_bytes_total`, `node_network_transmit_bytes_total` - Réseau
- `node_load1`, `node_load5`, `node_load15` - Load average

### cAdvisor (Docker)

- `container_cpu_usage_seconds_total` - CPU conteneurs
- `container_memory_usage_bytes` - Mémoire conteneurs
- `container_network_receive_bytes_total` - Réseau conteneurs
- `container_fs_usage_bytes` - Disque conteneurs

### PostgreSQL Exporter

- `pg_up` - Status PostgreSQL
- `pg_stat_database_numbackends` - Connexions actives
- `pg_stat_database_xact_commit` - Transactions
- `pg_stat_database_blks_read` - I/O reads
- `pg_replication_lag` - Replication lag

### Redis Exporter

- `redis_up` - Status Redis
- `redis_connected_clients` - Clients connectés
- `redis_memory_used_bytes` - Mémoire utilisée
- `redis_commands_processed_total` - Commandes

### Blackbox Exporter (HTTP)

- `probe_success` - Status HTTP
- `probe_http_duration_seconds` - Temps de réponse
- `probe_http_status_code` - Code HTTP
- `probe_ssl_earliest_cert_expiry` - Expiration SSL

---

## Troubleshooting

### Prometheus ne collecte pas les métriques

```bash
# Vérifier les targets
curl http://localhost:9090/api/v1/targets

# Vérifier la connectivité
curl http://10.x.x.10:9100/metrics

# Vérifier les logs Prometheus
docker logs prometheus
```

### Grafana ne se connecte pas à Prometheus

```bash
# Vérifier que Prometheus est accessible
docker exec grafana curl http://prometheus:9090/api/v1/status/config

# Vérifier les logs Grafana
docker logs grafana
```

### Alertes non envoyées

```bash
# Vérifier Alertmanager
docker logs alertmanager

# Tester la configuration SMTP
docker exec alertmanager amtool config routes test
```

### Logs non visibles dans Loki

```bash
# Vérifier Promtail
docker logs promtail

# Vérifier que Loki reçoit les logs
curl http://localhost:3100/loki/api/v1/label/job/values
```

---

## Maintenance

### Backup de Grafana

```bash
# Backup des dashboards
docker exec grafana grafana-cli admin export-dashboards /backup/
docker cp grafana:/backup ./grafana-backup-$(date +%Y%m%d).tar.gz
```

### Nettoyage des Données

```bash
# Prometheus (retention 30 jours par défaut)
# Loki (retention 30 jours configuré)

# Nettoyer manuellement si besoin
docker exec prometheus promtool tsdb clean
```

### Mises à Jour

```bash
# Mettre à jour les images
docker-compose pull
docker-compose up -d
```

---

## Ressources

- **Prometheus Docs**: https://prometheus.io/docs/
- **Grafana Docs**: https://grafana.com/docs/
- **Loki Docs**: https://grafana.com/docs/loki/
- **PromQL Cheat Sheet**: https://promlabs.com/promql-cheat-sheet/

---

**Version:** 1.0
**Date:** 2026-01-03
**Projet:** SIMVEB - Monitoring & Observability
