# SIMVEB - Stack de Monitoring et Observabilité

## Vue d'ensemble

Stack complète de monitoring pour SIMVEB incluant métriques, logs, alertes et dashboards.

## Composants

- **Prometheus** - Collecte et stockage de métriques
- **Grafana** - Visualisation et dashboards
- **Loki** - Agrégation de logs
- **Alertmanager** - Gestion des alertes
- **Exporters** - Node, cAdvisor, PostgreSQL, Redis, Blackbox

## Démarrage Rapide

```bash
# 1. Configurer les variables
cp .env.example .env
nano .env

# 2. Mettre à jour les IPs dans prometheus/prometheus.yml

# 3. Démarrer le stack
docker-compose up -d

# 4. Accéder à Grafana
http://your-monitoring-server:3000
Login: admin
Password: (voir .env)
```

## Accès

| Service | Port | URL |
|---------|------|-----|
| Grafana | 3000 | http://monitoring-vm:3000 |
| Prometheus | 9090 | http://monitoring-vm:9090 |
| Alertmanager | 9093 | http://monitoring-vm:9093 |
| Loki | 3100 | http://monitoring-vm:3100 |

## Installation sur les VMs

### VMs App (Staging & Production)

```bash
# Node Exporter
docker run -d --name=node-exporter --restart=unless-stopped --net="host" --pid="host" -v "/:/host:ro,rslave" prom/node-exporter:latest --path.rootfs=/host

# cAdvisor
docker run -d --name=cadvisor --restart=unless-stopped --privileged --volume=/:/rootfs:ro --volume=/var/run:/var/run:ro --volume=/sys:/sys:ro --volume=/var/lib/docker/:/var/lib/docker:ro --publish=8080:8080 gcr.io/cadvisor/cadvisor:latest
```

### VMs DB (Staging & Production)

```bash
# Node Exporter
docker run -d --name=node-exporter --restart=unless-stopped --net="host" --pid="host" -v "/:/host:ro,rslave" prom/node-exporter:latest --path.rootfs=/host

# PostgreSQL Exporter
docker run -d --name=postgres-exporter --restart=unless-stopped -e DATA_SOURCE_NAME="postgresql://user:pass@localhost:5432/db?sslmode=require" --publish=9187:9187 prometheuscommunity/postgres-exporter:latest
```

## Configuration

### Prometheus

Fichier: `prometheus/prometheus.yml`

Mettre à jour les IPs des targets:
- Node Exporters (VMs)
- cAdvisor (VMs App)
- PostgreSQL Exporters (VMs DB)
- URLs des services (Blackbox)

### Alertes

Fichier: `prometheus/alerts/simveb_alerts.yml`

Règles d'alerte pour:
- Système (CPU, RAM, Disk)
- Docker (conteneurs)
- PostgreSQL
- HTTP/HTTPS
- SSL certificates

### Alertmanager

Fichier: `alertmanager/config.yml`

Configurer:
- Email (SMTP)
- Slack (Webhook)
- Équipes de réception

## Dashboards Grafana

### Dashboards Recommandés

Importer via ID:
- **1860** - Node Exporter Full
- **10619** - Docker Container & Host Metrics
- **9628** - PostgreSQL Database
- **11835** - Redis
- **13639** - Loki Logs

### Dashboards Personnalisés

Créer des dashboards pour:
- Vue d'ensemble SIMVEB
- Application metrics (Laravel)
- Business metrics

## Alertes

### Niveaux de Sévérité

- **Critical** - Nécessite intervention immédiate
- **Warning** - À surveiller, intervention possible
- **Info** - Informatif

### Canaux de Notification

- Email (pour tous)
- Slack (par environnement/catégorie)

## Logs avec Loki

### Logs Collectés

- Système (syslog, auth)
- Docker (conteneurs)
- Nginx (access, error)
- Laravel (application)
- PostgreSQL
- Fail2Ban

### Requêtes Utiles

```logql
# Erreurs Laravel
{job="laravel"} |= "ERROR"

# Nginx 5xx
{job="nginx", type="access"} | json | status >= 500

# SSH failed
{job="auth"} |= "Failed password"
```

## Maintenance

### Backup

```bash
# Grafana dashboards
docker exec grafana grafana-cli admin export-dashboards /backup/
```

### Nettoyage

```bash
# Prometheus data (retention: 30 jours)
# Loki data (retention: 30 jours)
# Automatique selon configuration
```

### Mises à Jour

```bash
docker-compose pull
docker-compose up -d
```

## Troubleshooting

### Prometheus ne scrape pas

```bash
# Vérifier targets
curl http://localhost:9090/api/v1/targets

# Tester connectivité
curl http://target-ip:port/metrics

# Logs
docker logs prometheus
```

### Alertes non reçues

```bash
# Vérifier Alertmanager
docker logs alertmanager

# Tester SMTP
docker exec alertmanager amtool check-config /etc/alertmanager/config.yml
```

## Documentation Complète

Voir [MONITORING_GUIDE.md](./MONITORING_GUIDE.md) pour la documentation détaillée.

## Support

- **Prometheus**: https://prometheus.io/docs/
- **Grafana**: https://grafana.com/docs/
- **Loki**: https://grafana.com/docs/loki/

---

**Version:** 1.0
**Date:** 2026-01-03
