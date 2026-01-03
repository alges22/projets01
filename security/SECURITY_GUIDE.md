# Guide de Sécurisation SIMVEB

## Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Sécurisation des VMs](#sécurisation-des-vms)
3. [Sécurisation PostgreSQL](#sécurisation-postgresql)
4. [Sécurisation Docker](#sécurisation-docker)
5. [SSL/TLS](#ssltls)
6. [Gestion des Secrets](#gestion-des-secrets)
7. [Firewall et Réseau](#firewall-et-réseau)
8. [Audit et Logs](#audit-et-logs)
9. [Backup et Recovery](#backup-et-recovery)
10. [Checklist Sécurité](#checklist-sécurité)

---

## Vue d'ensemble

### Principe de Défense en Profondeur

```
┌─────────────────────────────────────────────────┐
│ Layer 1: Réseau & Firewall                      │
│  - UFW/iptables                                 │
│  - Rate limiting                                │
│  - VPN/IP Whitelisting                          │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│ Layer 2: Système d'exploitation                 │
│  - Hardening OS                                 │
│  - Fail2Ban                                     │
│  - SELinux/AppArmor                             │
│  - Updates automatiques                         │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│ Layer 3: SSH & Accès                            │
│  - SSH key-only                                 │
│  - 2FA                                          │
│  - Bastion host                                 │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│ Layer 4: Services (Docker, PostgreSQL)          │
│  - Docker security                              │
│  - PostgreSQL hardening                         │
│  - Secrets management                           │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│ Layer 5: Application                            │
│  - HTTPS/SSL                                    │
│  - WAF (Web Application Firewall)               │
│  - Rate limiting API                            │
│  - Input validation                             │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│ Layer 6: Monitoring & Audit                     │
│  - Logs centralisés                             │
│  - Alertes                                      │
│  - Intrusion detection                          │
└─────────────────────────────────────────────────┘
```

---

## Sécurisation des VMs

### 1. Hardening Système de Base

#### Installation des Outils de Sécurité

```bash
#!/bin/bash
# À exécuter sur chaque VM

# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation des outils de sécurité
sudo apt install -y \
    ufw \
    fail2ban \
    unattended-upgrades \
    apt-listchanges \
    needrestart \
    aide \
    rkhunter \
    chkrootkit \
    auditd \
    apparmor \
    apparmor-utils

# Activer les mises à jour automatiques de sécurité
sudo dpkg-reconfigure -plow unattended-upgrades
```

#### Configuration SSH Sécurisée

```bash
# Éditer /etc/ssh/sshd_config
sudo nano /etc/ssh/sshd_config
```

Configuration recommandée :

```
# /etc/ssh/sshd_config - Configuration Sécurisée

# Port non-standard (optionnel, mais recommandé)
Port 2222

# Désactiver root login
PermitRootLogin no

# Authentification par clé uniquement
PubkeyAuthentication yes
PasswordAuthentication no
PermitEmptyPasswords no
ChallengeResponseAuthentication no

# Limiter les utilisateurs autorisés
AllowUsers simveb

# Désactiver X11 forwarding
X11Forwarding no

# Limiter les tentatives de connexion
MaxAuthTries 3
MaxSessions 5

# Timeout inactivité
ClientAliveInterval 300
ClientAliveCountMax 2

# Protocole 2 uniquement
Protocol 2

# Algorithmes sécurisés
KexAlgorithms curve25519-sha256@libssh.org,diffie-hellman-group-exchange-sha256
Ciphers chacha20-poly1305@openssh.com,aes256-gcm@openssh.com,aes128-gcm@openssh.com,aes256-ctr,aes192-ctr,aes128-ctr
MACs hmac-sha2-512-etm@openssh.com,hmac-sha2-256-etm@openssh.com,hmac-sha2-512,hmac-sha2-256

# Logs
SyslogFacility AUTH
LogLevel VERBOSE

# Banner d'avertissement
Banner /etc/ssh/banner.txt
```

Créer le banner :

```bash
sudo nano /etc/ssh/banner.txt
```

```
***************************************************************************
                    ACCES AUTORISE UNIQUEMENT

Ce système est la propriété de SIMVEB. L'accès non autorisé est interdit
et sera poursuivi conformément à la loi. Toutes les activités sont
surveillées et enregistrées.

***************************************************************************
```

Redémarrer SSH :

```bash
sudo systemctl restart sshd
```

#### Configuration Fail2Ban

```bash
# Créer la configuration locale
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local
```

Configuration :

```ini
[DEFAULT]
# Ban pour 1 heure après 5 tentatives en 10 minutes
bantime = 3600
findtime = 600
maxretry = 5

# Email notifications
destemail = security@simveb-bj.com
sendername = Fail2Ban-SIMVEB
action = %(action_mwl)s

[sshd]
enabled = true
port = 2222
logpath = /var/log/auth.log
maxretry = 3
bantime = 7200

[sshd-ddos]
enabled = true
port = 2222
logpath = /var/log/auth.log

# Protection Nginx (sur VMs App)
[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-botsearch]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 2
```

Démarrer Fail2Ban :

```bash
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
sudo fail2ban-client status
```

#### Hardening Kernel

```bash
# Éditer /etc/sysctl.conf
sudo nano /etc/sysctl.conf
```

Ajouter :

```bash
# Protection contre SYN flood
net.ipv4.tcp_syncookies = 1
net.ipv4.tcp_max_syn_backlog = 2048
net.ipv4.tcp_synack_retries = 2
net.ipv4.tcp_syn_retries = 5

# Protection IP spoofing
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1

# Ignorer ICMP redirects
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.secure_redirects = 0
net.ipv4.conf.default.secure_redirects = 0
net.ipv6.conf.all.accept_redirects = 0
net.ipv6.conf.default.accept_redirects = 0

# Ne pas envoyer ICMP redirects
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0

# Ignorer ICMP pings
net.ipv4.icmp_echo_ignore_all = 0
net.ipv4.icmp_echo_ignore_broadcasts = 1

# Logs des paquets suspects
net.ipv4.conf.all.log_martians = 1
net.ipv4.conf.default.log_martians = 1

# Protection contre bad ICMP error messages
net.ipv4.icmp_ignore_bogus_error_responses = 1

# Désactiver IPv6 si non utilisé
net.ipv6.conf.all.disable_ipv6 = 1
net.ipv6.conf.default.disable_ipv6 = 1

# Protection kernel
kernel.dmesg_restrict = 1
kernel.kptr_restrict = 2
```

Appliquer :

```bash
sudo sysctl -p
```

#### Mises à Jour Automatiques

```bash
# Configuration des mises à jour automatiques
sudo nano /etc/apt/apt.conf.d/50unattended-upgrades
```

```
Unattended-Upgrade::Allowed-Origins {
    "${distro_id}:${distro_codename}-security";
    "${distro_id}ESMApps:${distro_codename}-apps-security";
    "${distro_id}ESM:${distro_codename}-infra-security";
};

Unattended-Upgrade::AutoFixInterruptedDpkg "true";
Unattended-Upgrade::MinimalSteps "true";
Unattended-Upgrade::Remove-Unused-Kernel-Packages "true";
Unattended-Upgrade::Remove-Unused-Dependencies "true";
Unattended-Upgrade::Automatic-Reboot "true";
Unattended-Upgrade::Automatic-Reboot-Time "03:00";

Unattended-Upgrade::Mail "security@simveb-bj.com";
Unattended-Upgrade::MailReport "only-on-error";
```

---

## Sécurisation PostgreSQL

### 1. Hardening PostgreSQL

#### Configuration postgresql.conf

```bash
# Sur VMs DB
sudo nano /etc/postgresql/15/main/postgresql.conf
```

```ini
# Connexions
listen_addresses = '10.x.x.20,127.0.0.1'  # IP spécifique seulement
max_connections = 100
superuser_reserved_connections = 3

# SSL/TLS OBLIGATOIRE
ssl = on
ssl_cert_file = '/etc/ssl/certs/postgresql.crt'
ssl_key_file = '/etc/ssl/private/postgresql.key'
ssl_ca_file = '/etc/ssl/certs/ca.crt'
ssl_prefer_server_ciphers = on
ssl_min_protocol_version = 'TLSv1.2'
ssl_ciphers = 'HIGH:MEDIUM:+3DES:!aNULL'

# Logs détaillés
logging_collector = on
log_directory = 'log'
log_filename = 'postgresql-%Y-%m-%d_%H%M%S.log'
log_rotation_age = 1d
log_rotation_size = 100MB
log_min_duration_statement = 1000  # Log queries > 1s
log_connections = on
log_disconnections = on
log_duration = on
log_line_prefix = '%t [%p]: user=%u,db=%d,app=%a,client=%h '
log_statement = 'ddl'  # Log DDL statements
log_lock_waits = on

# Sécurité
password_encryption = scram-sha-256
```

#### Configuration pg_hba.conf Sécurisée

```bash
sudo nano /etc/postgresql/15/main/pg_hba.conf
```

```
# TYPE  DATABASE        USER            ADDRESS                 METHOD

# Local connections
local   all             postgres                                peer

# IPv4 connections - SSL OBLIGATOIRE
hostssl simveb_staging  simveb          10.x.x.10/32            scram-sha-256
hostssl simveb_production simveb        10.x.x.30/32            scram-sha-256

# Rejeter tout le reste
host    all             all             0.0.0.0/0               reject
```

#### Créer les Certificats SSL

```bash
# Sur VM DB
cd /etc/ssl/certs

# Générer certificat auto-signé (ou utiliser Let's Encrypt)
sudo openssl req -new -x509 -days 365 -nodes \
    -text -out postgresql.crt \
    -keyout /etc/ssl/private/postgresql.key \
    -subj "/CN=db.simveb-bj.com"

sudo chmod 600 /etc/ssl/private/postgresql.key
sudo chown postgres:postgres /etc/ssl/private/postgresql.key
sudo chown postgres:postgres /etc/ssl/certs/postgresql.crt

# Redémarrer PostgreSQL
sudo systemctl restart postgresql
```

#### Auditing PostgreSQL

```bash
# Installer pgAudit
sudo apt install -y postgresql-15-pgaudit

# Activer dans postgresql.conf
sudo nano /etc/postgresql/15/main/postgresql.conf
```

Ajouter :

```ini
shared_preload_libraries = 'pgaudit'
pgaudit.log = 'write, ddl'
pgaudit.log_catalog = off
pgaudit.log_parameter = on
pgaudit.log_relation = on
```

#### Rotation des Mots de Passe

```bash
# Script de rotation mensuelle
sudo nano /opt/simveb/security/rotate-db-password.sh
```

```bash
#!/bin/bash
# Rotation automatique du mot de passe PostgreSQL

NEW_PASSWORD=$(openssl rand -base64 32)
OLD_PASSWORD=$(cat /opt/simveb/.db_password)

# Changer le mot de passe
sudo -u postgres psql -c "ALTER USER simveb WITH PASSWORD '$NEW_PASSWORD';"

# Sauvegarder le nouveau mot de passe
echo "$NEW_PASSWORD" > /opt/simveb/.db_password
chmod 600 /opt/simveb/.db_password

# Mettre à jour les variables GitLab CI/CD (via API)
# TODO: Implémenter l'update via GitLab API

echo "Password rotated successfully"
```

---

## Sécurisation Docker

### 1. Hardening Docker Daemon

```bash
# Créer la configuration Docker daemon
sudo nano /etc/docker/daemon.json
```

```json
{
  "icc": false,
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  },
  "live-restore": true,
  "userland-proxy": false,
  "no-new-privileges": true,
  "seccomp-profile": "/etc/docker/seccomp.json",
  "userns-remap": "default"
}
```

### 2. Docker Security Best Practices

#### Images

```yaml
# Dans docker-compose.yml - Bonnes pratiques

services:
  backend:
    image: ${CI_REGISTRY_IMAGE}/backend:${CI_COMMIT_SHA}

    # Sécurité
    security_opt:
      - no-new-privileges:true
      - apparmor:docker-default

    # Capabilities limitées
    cap_drop:
      - ALL
    cap_add:
      - CHOWN
      - SETGID
      - SETUID
      - DAC_OVERRIDE

    # Read-only filesystem (sauf volumes nécessaires)
    read_only: true
    tmpfs:
      - /tmp
      - /var/run

    # Limites de ressources
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
          pids: 200
        reservations:
          cpus: '1'
          memory: 1G

    # Healthcheck
    healthcheck:
      test: ["CMD", "php", "artisan", "health:check"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
```

### 3. Scan des Vulnérabilités

```bash
# Installer Trivy
wget -qO - https://aquasecurity.github.io/trivy-repo/deb/public.key | sudo apt-key add -
echo "deb https://aquasecurity.github.io/trivy-repo/deb $(lsb_release -sc) main" | sudo tee -a /etc/apt/sources.list.d/trivy.list
sudo apt update
sudo apt install trivy

# Scanner une image
trivy image ${CI_REGISTRY_IMAGE}/backend:latest

# Scanner avec seuil de sévérité
trivy image --severity HIGH,CRITICAL ${CI_REGISTRY_IMAGE}/backend:latest
```

---

## SSL/TLS

### 1. Certificats Let's Encrypt

```bash
# Installer Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtenir certificats pour tous les domaines
sudo certbot --nginx -d simveb-bj.com -d www.simveb-bj.com \
    -d api.simveb-bj.com \
    -d admin.simveb-bj.com \
    -d affiliate.simveb-bj.com \
    --email security@simveb-bj.com \
    --agree-tos \
    --no-eff-email

# Renouvellement automatique
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

### 2. Configuration Nginx SSL

```nginx
# /etc/nginx/sites-available/simveb-ssl.conf

# Redirection HTTP vers HTTPS
server {
    listen 80;
    server_name simveb-bj.com www.simveb-bj.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS Configuration
server {
    listen 443 ssl http2;
    server_name simveb-bj.com www.simveb-bj.com;

    # SSL Certificates
    ssl_certificate /etc/letsencrypt/live/simveb-bj.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/simveb-bj.com/privkey.pem;

    # SSL Configuration Moderne
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers off;

    # HSTS
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:;" always;

    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate /etc/letsencrypt/live/simveb-bj.com/chain.pem;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;

    # Session Cache
    ssl_session_cache shared:SSL:50m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api_limit:10m rate=10r/s;
    limit_req zone=api_limit burst=20 nodelay;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
```

---

## Firewall et Réseau

### Configuration UFW

#### VM App Staging/Production

```bash
# Réinitialiser UFW
sudo ufw --force reset

# Politique par défaut
sudo ufw default deny incoming
sudo ufw default allow outgoing

# SSH (port custom)
sudo ufw allow 2222/tcp comment 'SSH'

# HTTP/HTTPS
sudo ufw allow 80/tcp comment 'HTTP'
sudo ufw allow 443/tcp comment 'HTTPS'

# Monitoring (seulement depuis IP spécifiques)
sudo ufw allow from 10.x.x.50 to any port 9090 comment 'Prometheus'
sudo ufw allow from 10.x.x.50 to any port 3100 comment 'Loki'

# Rate limiting SSH
sudo ufw limit 2222/tcp

# Activer
sudo ufw --force enable
sudo ufw status verbose
```

#### VM DB Staging/Production

```bash
# Politique par défaut
sudo ufw default deny incoming
sudo ufw default allow outgoing

# SSH
sudo ufw allow 2222/tcp comment 'SSH'

# PostgreSQL - UNIQUEMENT depuis VMs App
sudo ufw allow from 10.x.x.10 to any port 5432 comment 'PostgreSQL from Staging App'
sudo ufw allow from 10.x.x.30 to any port 5432 comment 'PostgreSQL from Prod App'

# PostgreSQL Exporter - UNIQUEMENT depuis monitoring
sudo ufw allow from 10.x.x.50 to any port 9187 comment 'PostgreSQL Exporter'

# Activer
sudo ufw --force enable
sudo ufw status verbose
```

---

## Gestion des Secrets

### 1. HashiCorp Vault (Recommandé pour Production)

```bash
# Installation Vault sur une VM dédiée
wget -O- https://apt.releases.hashicorp.com/gpg | sudo gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update && sudo apt install vault

# Configuration Vault
sudo nano /etc/vault.d/vault.hcl
```

```hcl
storage "file" {
  path = "/opt/vault/data"
}

listener "tcp" {
  address     = "0.0.0.0:8200"
  tls_cert_file = "/etc/vault.d/vault.crt"
  tls_key_file  = "/etc/vault.d/vault.key"
}

api_addr = "https://vault.simveb-bj.com:8200"
cluster_addr = "https://vault.simveb-bj.com:8201"
ui = true
```

### 2. Secrets dans GitLab CI/CD

- ✅ Utiliser les variables protégées
- ✅ Masquer les secrets
- ✅ Limiter aux branches protégées
- ✅ Rotation régulière
- ✅ Audit des accès

### 3. Secrets dans Docker

```bash
# Utiliser Docker secrets au lieu de variables d'environnement
echo "mon_secret" | docker secret create db_password -

# Dans docker-compose.yml
secrets:
  db_password:
    external: true

services:
  backend:
    secrets:
      - db_password
```

---

## Audit et Logs

### 1. Audit Système (auditd)

```bash
# Configuration auditd
sudo nano /etc/audit/rules.d/simveb.rules
```

```bash
# Surveiller les modifications de fichiers sensibles
-w /etc/passwd -p wa -k passwd_changes
-w /etc/shadow -p wa -k shadow_changes
-w /etc/group -p wa -k group_changes
-w /etc/sudoers -p wa -k sudoers_changes
-w /etc/ssh/sshd_config -p wa -k sshd_config_changes

# Surveiller Docker
-w /var/lib/docker -p wa -k docker_changes
-w /etc/docker -p wa -k docker_config_changes

# Surveiller PostgreSQL
-w /etc/postgresql -p wa -k postgresql_config_changes
-w /var/lib/postgresql -p wa -k postgresql_data_changes

# Commandes sensibles
-a always,exit -F arch=b64 -S execve -F path=/usr/bin/docker -k docker_commands
-a always,exit -F arch=b64 -S execve -F path=/usr/bin/git -k git_commands
```

Activer :

```bash
sudo augenrules --load
sudo systemctl restart auditd
```

### 2. Rotation des Logs

```bash
# Configuration logrotate
sudo nano /etc/logrotate.d/simveb
```

```
/opt/simveb/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 simveb simveb
    sharedscripts
    postrotate
        docker exec simveb-backend-prod php artisan log:clear --days=30
    endscript
}

/var/log/nginx/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        [ -f /var/run/nginx.pid ] && kill -USR1 `cat /var/run/nginx.pid`
    endscript
}
```

---

## Checklist Sécurité

### Checklist VM App

- [ ] SSH configuré (clés seulement, port custom)
- [ ] Fail2Ban actif
- [ ] UFW configuré
- [ ] Mises à jour automatiques activées
- [ ] Docker sécurisé (daemon.json)
- [ ] SSL/TLS configuré
- [ ] Nginx headers sécurité
- [ ] Logs centralisés
- [ ] Monitoring actif
- [ ] Backups configurés
- [ ] Secrets rotatés

### Checklist VM DB

- [ ] SSH configuré
- [ ] UFW configuré (PostgreSQL uniquement depuis App VMs)
- [ ] PostgreSQL SSL obligatoire
- [ ] pg_hba.conf restrictif
- [ ] Auditing activé
- [ ] Logs détaillés
- [ ] Backups automatiques quotidiens
- [ ] Monitoring actif
- [ ] Rotation mots de passe

### Checklist Application

- [ ] HTTPS partout
- [ ] Headers sécurité
- [ ] Rate limiting API
- [ ] Validation input
- [ ] CORS configuré
- [ ] CSP configuré
- [ ] Secrets en variables d'environnement
- [ ] Logs applicatifs
- [ ] Sentry configuré

---

## Scripts Automatisés

Tous les scripts de hardening automatisés sont disponibles dans `security/scripts/`.

```bash
# Hardening complet d'une VM
bash security/scripts/harden-vm.sh

# Vérification sécurité
bash security/scripts/security-audit.sh
```

---

**Version:** 1.0
**Date:** 2026-01-03
**Classification:** Confidentiel
