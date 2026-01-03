# SIMVEB - Sécurité et Monitoring

Guide complet de sécurisation et monitoring du projet SIMVEB.

## 📚 Table des Matières

1. [Sécurité](#sécurité)
2. [Monitoring](#monitoring)
3. [Démarrage Rapide](#démarrage-rapide)
4. [Architecture](#architecture)

---

## 🔒 Sécurité

### Documentation

- **[Guide de Sécurité Complet](security/SECURITY_GUIDE.md)** - Hardening complet des VMs
- **[Scripts d'Automatisation](security/scripts/)** - Scripts de sécurisation automatisés

### Scripts Disponibles

```bash
# Hardening complet d'une VM
sudo bash security/scripts/harden-vm.sh

# Audit de sécurité
sudo bash security/scripts/security-audit.sh
```

### Mesures de Sécurité Implémentées

#### ✅ Niveau Système

- SSH durci (port custom, clés seulement, no root)
- Fail2Ban actif
- Firewall UFW configuré
- Mises à jour automatiques
- Kernel hardening
- Auditd pour l'audit système
- Désactivation services inutiles

#### ✅ Niveau Réseau

- Firewall restrictif par VM
- Accès PostgreSQL limité aux IPs autorisées
- Rate limiting
- Protection DDoS basique
- VPN recommandé (à mettre en place)

#### ✅ Niveau Application

- SSL/TLS obligatoire partout
- Headers de sécurité HTTP
- CORS configuré
- CSP (Content Security Policy)
- Secrets management
- Docker sécurisé

#### ✅ Niveau Base de Données

- PostgreSQL SSL obligatoire
- pg_hba.conf restrictif
- Password encryption: scram-sha-256
- Auditing activé
- Backups automatiques

### Checklist Sécurité

**Avant Mise en Production:**

- [ ] SSH configuré (clés, port custom, no root)
- [ ] Fail2Ban actif
- [ ] UFW configuré sur toutes les VMs
- [ ] PostgreSQL SSL activé
- [ ] Certificats SSL valides
- [ ] Secrets rotatés
- [ ] Auditing activé
- [ ] Backups configurés
- [ ] Monitoring actif
- [ ] Tests de pénétration effectués

### Maintenance Sécurité

**Hebdomadaire:**
- Vérifier les logs Fail2Ban
- Vérifier les alertes de sécurité
- Audit rapide avec `security-audit.sh`

**Mensuel:**
- Rotation des mots de passe DB
- Scan de vulnérabilités (Trivy)
- Revue des accès SSH
- Vérification certificats SSL

**Trimestriel:**
- Audit de sécurité complet
- Pentest externe
- Revue des permissions
- Update scripts de hardening

---

## 📊 Monitoring

### Documentation

- **[Guide de Monitoring Complet](monitoring/MONITORING_GUIDE.md)** - Setup et utilisation
- **[README Monitoring](monitoring/README.md)** - Démarrage rapide

### Stack de Monitoring

- **Prometheus** - Métriques
- **Grafana** - Visualisation
- **Loki** - Logs
- **Alertmanager** - Alertes
- **Exporters** - Node, cAdvisor, PostgreSQL, Redis

### Métriques Surveillées

```
✅ Système:
   - CPU, RAM, Disk
   - Network I/O
   - Load average

✅ Docker:
   - Conteneurs actifs
   - Ressources par conteneur
   - Redémarrages

✅ Applications:
   - Disponibilité HTTP/HTTPS
   - Temps de réponse
   - Codes erreur
   - Queue Laravel

✅ Bases de données:
   - PostgreSQL connexions, queries
   - Redis mémoire, clients
   - Slow queries

✅ Sécurité:
   - Certificats SSL (expiration)
   - Fail2Ban (bans)
   - Logs authentification
```

### Alertes Configurées

**Critiques (notification immédiate):**
- VM Down
- Service Down
- PostgreSQL Down
- Disk < 10%
- Memory > 95%
- SSL < 7 jours

**Warnings (notification normale):**
- CPU > 80%
- Memory > 80%
- Disk < 20%
- Slow queries
- SSL < 30 jours

### Dashboards Grafana

Dashboards par catégorie:
- Vue d'ensemble système
- Applications SIMVEB
- Bases de données
- Sécurité
- Logs (Loki)

### Accès

| Service | URL | Credentials |
|---------|-----|-------------|
| Grafana | http://monitoring-vm:3000 | admin / (voir .env) |
| Prometheus | http://monitoring-vm:9090 | - |
| Alertmanager | http://monitoring-vm:9093 | - |

---

## 🚀 Démarrage Rapide

### 1. Sécurisation des VMs

```bash
# Sur chaque VM (App et DB, Staging et Production)

# 1. Télécharger le script
wget https://your-repo/security/scripts/harden-vm.sh

# 2. Rendre exécutable
chmod +x harden-vm.sh

# 3. Exécuter (en root)
sudo ./harden-vm.sh

# 4. Suivre les instructions
# Type de VM: app ou db
# Environnement: staging ou production

# 5. Tester la nouvelle connexion SSH
ssh -p 2222 -i ~/.ssh/votre_cle simveb@VM_IP
```

### 2. Installation Monitoring

```bash
# Sur le serveur de monitoring (ou une VM App si ressources OK)

# 1. Cloner les fichiers monitoring
cd /opt
sudo mkdir simveb-monitoring
cd simveb-monitoring

# 2. Copier les fichiers
# - docker-compose.yml
# - prometheus/
# - grafana/
# - loki/
# - alertmanager/

# 3. Configurer .env
cat > .env << 'EOF'
GRAFANA_ADMIN_PASSWORD=VotreMotDePasseSecurise
SMTP_USER=email@gmail.com
SMTP_PASSWORD=VotrePassword
SLACK_WEBHOOK_URL=https://hooks.slack.com/...
DB_HOST_STAGING=10.x.x.20
DB_HOST_PROD=10.x.x.40
DB_PASSWORD_STAGING=password
DB_PASSWORD_PROD=password
REDIS_PASSWORD=password
EOF

# 4. Mettre à jour les IPs dans prometheus.yml
nano prometheus/prometheus.yml

# 5. Démarrer
docker-compose up -d

# 6. Vérifier
docker-compose ps
docker-compose logs
```

### 3. Installer les Exporters sur les VMs

**Sur VMs App:**

```bash
# Node Exporter
docker run -d --name=node-exporter --restart=unless-stopped \
  --net="host" --pid="host" -v "/:/host:ro,rslave" \
  prom/node-exporter:latest --path.rootfs=/host

# cAdvisor
docker run -d --name=cadvisor --restart=unless-stopped \
  --privileged --volume=/:/rootfs:ro \
  --volume=/var/run:/var/run:ro \
  --volume=/sys:/sys:ro \
  --volume=/var/lib/docker/:/var/lib/docker:ro \
  --publish=8080:8080 \
  gcr.io/cadvisor/cadvisor:latest
```

**Sur VMs DB:**

```bash
# Node Exporter
docker run -d --name=node-exporter --restart=unless-stopped \
  --net="host" --pid="host" -v "/:/host:ro,rslave" \
  prom/node-exporter:latest --path.rootfs=/host

# PostgreSQL Exporter
docker run -d --name=postgres-exporter --restart=unless-stopped \
  -e DATA_SOURCE_NAME="postgresql://simveb:password@localhost:5432/simveb_staging?sslmode=require" \
  --publish=9187:9187 \
  prometheuscommunity/postgres-exporter:latest
```

### 4. Configurer le Firewall

```bash
# Sur VM Monitoring
sudo ufw allow from VOTRE_IP to any port 3000 comment 'Grafana'

# Sur VMs App/DB
sudo ufw allow from MONITORING_VM_IP to any port 9100 comment 'Node Exporter'
sudo ufw allow from MONITORING_VM_IP to any port 8080 comment 'cAdvisor'
sudo ufw allow from MONITORING_VM_IP to any port 9187 comment 'PostgreSQL Exporter'
```

### 5. Accéder à Grafana

```
http://monitoring-vm:3000
Login: admin
Password: (voir .env)

1. Data Sources > Add Prometheus
2. Data Sources > Add Loki
3. Dashboards > Import > ID: 1860 (Node Exporter)
4. Dashboards > Import > ID: 9628 (PostgreSQL)
```

---

## 🏗️ Architecture

### Sécurité - Défense en Profondeur

```
┌─────────────────────────────────────┐
│ Layer 1: Réseau & Firewall          │  UFW, IP Whitelisting
├─────────────────────────────────────┤
│ Layer 2: OS Hardening                │  SSH, Fail2Ban, Kernel
├─────────────────────────────────────┤
│ Layer 3: Services                    │  Docker, PostgreSQL
├─────────────────────────────────────┤
│ Layer 4: Application                 │  HTTPS, Headers, WAF
├─────────────────────────────────────┤
│ Layer 5: Monitoring & Audit          │  Logs, Alertes
└─────────────────────────────────────┘
```

### Monitoring - Collecte et Alertes

```
┌──────────────────────────────────────┐
│       VM Monitoring (10.x.x.50)      │
│                                      │
│  Prometheus ←─ Exporters (VMs)      │
│  Grafana ←────── Prometheus          │
│  Loki ←────────── Promtail (VMs)     │
│  Alertmanager ─→ Email/Slack        │
└──────────────────────────────────────┘
           │
    ┌──────┴──────┐
    │             │
┌───▼────┐   ┌───▼────┐
│VM App  │   │VM DB   │
│Staging │   │Staging │
└────────┘   └────────┘
┌────────┐   ┌────────┐
│VM App  │   │VM DB   │
│ Prod   │   │ Prod   │
└────────┘   └────────┘
```

---

## 📋 Checklist Complète

### Sécurité

**Système:**
- [ ] SSH durci (port 2222, clés, no root)
- [ ] Fail2Ban actif
- [ ] UFW configuré
- [ ] Mises à jour auto
- [ ] Kernel hardening
- [ ] Auditd activé

**Réseau:**
- [ ] Firewall par VM
- [ ] PostgreSQL accessible uniquement depuis VMs App
- [ ] SSL/TLS partout

**Application:**
- [ ] Certificats SSL valides
- [ ] Headers sécurité
- [ ] Secrets management
- [ ] Docker sécurisé

**Base de données:**
- [ ] PostgreSQL SSL
- [ ] pg_hba.conf restrictif
- [ ] Auditing
- [ ] Backups quotidiens

### Monitoring

**Infrastructure:**
- [ ] Prometheus déployé
- [ ] Grafana configuré
- [ ] Loki pour logs
- [ ] Alertmanager configuré

**Exporters:**
- [ ] Node Exporter sur toutes VMs
- [ ] cAdvisor sur VMs App
- [ ] PostgreSQL Exporter sur VMs DB
- [ ] Promtail sur toutes VMs

**Alertes:**
- [ ] Email configuré (SMTP)
- [ ] Slack configuré (webhook)
- [ ] Règles d'alerte testées
- [ ] Notifications reçues

**Dashboards:**
- [ ] Dashboard système
- [ ] Dashboard applications
- [ ] Dashboard bases de données
- [ ] Dashboard sécurité

---

## 🔧 Maintenance

### Quotidienne

- Vérifier dashboards Grafana
- Vérifier alertes critiques

### Hebdomadaire

- Audit sécurité avec `security-audit.sh`
- Vérifier logs Fail2Ban
- Vérifier espace disque

### Mensuelle

- Rotation mots de passe
- Revue des alertes
- Scan vulnérabilités
- Backup test restore

### Trimestrielle

- Audit sécurité complet
- Pentest
- Revue permissions
- Update documentation

---

## 📚 Documentation

- [Security Guide](security/SECURITY_GUIDE.md) - Guide complet de sécurisation
- [Monitoring Guide](monitoring/MONITORING_GUIDE.md) - Guide complet de monitoring
- [Deployment Guide](DEPLOYMENT_GUIDE.md) - Guide de déploiement

---

## 🆘 Support

**Sécurité:**
- Email: security@simveb-bj.com
- Urgence: Alertes Slack #security-critical

**Monitoring:**
- Email: devops@simveb-bj.com
- Grafana: http://monitoring-vm:3000

**Escalation:**
- Tech Lead: tech-lead@simveb-bj.com
- CTO: cto@simveb-bj.com

---

**Version:** 1.0
**Date:** 2026-01-03
**Classification:** Confidentiel
**Projet:** SIMVEB - Infrastructure Sécurité & Monitoring
