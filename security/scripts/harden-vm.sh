#!/bin/bash

# ========================================
# SIMVEB - Script de Hardening VM
# Sécurisation automatique des VMs
# ========================================

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Vérifier root
if [ "$EUID" -ne 0 ]; then
    log_error "Ce script doit être exécuté en tant que root"
    exit 1
fi

echo ""
log_info "========================================="
log_info "  SIMVEB VM HARDENING SCRIPT"
log_info "========================================="
echo ""

read -p "Type de VM (app/db): " VM_TYPE
read -p "Environnement (staging/production): " ENVIRONMENT

log_info "Configuration: VM Type=$VM_TYPE, Environment=$ENVIRONMENT"
echo ""

# 1. Mise à jour système
log_info "1. Mise à jour du système..."
apt update && apt upgrade -y
log_success "Système mis à jour"

# 2. Installation des outils de sécurité
log_info "2. Installation des outils de sécurité..."
apt install -y \
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
    apparmor-utils \
    iptables-persistent

log_success "Outils de sécurité installés"

# 3. Configuration SSH
log_info "3. Configuration SSH sécurisée..."

# Backup de la config SSH
cp /etc/ssh/sshd_config /etc/ssh/sshd_config.backup

# Nouvelle configuration SSH
cat > /etc/ssh/sshd_config << 'EOF'
Port 2222
Protocol 2
PermitRootLogin no
PubkeyAuthentication yes
PasswordAuthentication no
PermitEmptyPasswords no
ChallengeResponseAuthentication no
MaxAuthTries 3
MaxSessions 5
ClientAliveInterval 300
ClientAliveCountMax 2
X11Forwarding no
AllowUsers simveb

# Algorithmes sécurisés
KexAlgorithms curve25519-sha256@libssh.org,diffie-hellman-group-exchange-sha256
Ciphers chacha20-poly1305@openssh.com,aes256-gcm@openssh.com,aes128-gcm@openssh.com
MACs hmac-sha2-512-etm@openssh.com,hmac-sha2-256-etm@openssh.com

SyslogFacility AUTH
LogLevel VERBOSE

Banner /etc/ssh/banner.txt
EOF

# Créer le banner
cat > /etc/ssh/banner.txt << 'EOF'
***************************************************************************
                    ACCES AUTORISE UNIQUEMENT

Ce système est la propriété de SIMVEB. L'accès non autorisé est interdit
et sera poursuivi conformément à la loi. Toutes les activités sont
surveillées et enregistrées.

***************************************************************************
EOF

systemctl restart sshd
log_success "SSH configuré (Port 2222, clés seulement)"

# 4. Configuration Fail2Ban
log_info "4. Configuration Fail2Ban..."

cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5
destemail = security@simveb-bj.com
sendername = Fail2Ban-SIMVEB
action = %(action_mwl)s

[sshd]
enabled = true
port = 2222
logpath = /var/log/auth.log
maxretry = 3
bantime = 7200
EOF

systemctl enable fail2ban
systemctl restart fail2ban
log_success "Fail2Ban configuré"

# 5. Configuration UFW
log_info "5. Configuration du Firewall UFW..."

ufw --force reset
ufw default deny incoming
ufw default allow outgoing

# SSH
ufw allow 2222/tcp comment 'SSH'
ufw limit 2222/tcp

if [ "$VM_TYPE" == "app" ]; then
    # VM App
    ufw allow 80/tcp comment 'HTTP'
    ufw allow 443/tcp comment 'HTTPS'

    # Monitoring (à ajuster avec IP du serveur monitoring)
    # ufw allow from MONITORING_IP to any port 9090 comment 'Prometheus'
    # ufw allow from MONITORING_IP to any port 9100 comment 'Node Exporter'

    log_info "Firewall configuré pour VM App"
elif [ "$VM_TYPE" == "db" ]; then
    # VM DB - PostgreSQL uniquement depuis VMs App
    read -p "IP de la VM App autorisée pour PostgreSQL: " APP_IP
    ufw allow from $APP_IP to any port 5432 comment 'PostgreSQL'
    ufw allow from $APP_IP to any port 9187 comment 'PostgreSQL Exporter'

    log_info "Firewall configuré pour VM DB"
fi

ufw --force enable
log_success "UFW activé"

# 6. Hardening Kernel
log_info "6. Hardening du kernel..."

cat >> /etc/sysctl.conf << 'EOF'

# SIMVEB Security Hardening
net.ipv4.tcp_syncookies = 1
net.ipv4.tcp_max_syn_backlog = 2048
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.all.log_martians = 1
net.ipv4.icmp_echo_ignore_broadcasts = 1
net.ipv4.icmp_ignore_bogus_error_responses = 1
net.ipv6.conf.all.disable_ipv6 = 1
kernel.dmesg_restrict = 1
kernel.kptr_restrict = 2
fs.suid_dumpable = 0
EOF

sysctl -p
log_success "Kernel sécurisé"

# 7. Mises à jour automatiques
log_info "7. Configuration des mises à jour automatiques..."

cat > /etc/apt/apt.conf.d/50unattended-upgrades << 'EOF'
Unattended-Upgrade::Allowed-Origins {
    "${distro_id}:${distro_codename}-security";
};
Unattended-Upgrade::AutoFixInterruptedDpkg "true";
Unattended-Upgrade::MinimalSteps "true";
Unattended-Upgrade::Remove-Unused-Kernel-Packages "true";
Unattended-Upgrade::Remove-Unused-Dependencies "true";
Unattended-Upgrade::Automatic-Reboot "true";
Unattended-Upgrade::Automatic-Reboot-Time "03:00";
Unattended-Upgrade::Mail "security@simveb-bj.com";
Unattended-Upgrade::MailReport "only-on-error";
EOF

dpkg-reconfigure -plow unattended-upgrades
log_success "Mises à jour automatiques configurées"

# 8. Configuration Auditd
log_info "8. Configuration Auditd..."

cat > /etc/audit/rules.d/simveb.rules << 'EOF'
# Surveiller fichiers sensibles
-w /etc/passwd -p wa -k passwd_changes
-w /etc/shadow -p wa -k shadow_changes
-w /etc/group -p wa -k group_changes
-w /etc/sudoers -p wa -k sudoers_changes
-w /etc/ssh/sshd_config -p wa -k sshd_config

# Docker (si VM App)
-w /var/lib/docker -p wa -k docker_changes
-w /etc/docker -p wa -k docker_config

# PostgreSQL (si VM DB)
-w /etc/postgresql -p wa -k postgresql_config
-w /var/lib/postgresql -p wa -k postgresql_data
EOF

augenrules --load
systemctl restart auditd
log_success "Auditd configuré"

# 9. Hardening spécifique PostgreSQL (si VM DB)
if [ "$VM_TYPE" == "db" ]; then
    log_info "9. Hardening PostgreSQL..."

    # Note: La configuration détaillée de PostgreSQL nécessite une interaction
    log_warning "Configuration PostgreSQL à faire manuellement selon security/SECURITY_GUIDE.md"
    log_info "Points clés: SSL obligatoire, pg_hba.conf restrictif, logs détaillés"
fi

# 10. Hardening Docker (si VM App)
if [ "$VM_TYPE" == "app" ]; then
    log_info "10. Hardening Docker..."

    if command -v docker &> /dev/null; then
        mkdir -p /etc/docker
        cat > /etc/docker/daemon.json << 'EOF'
{
  "icc": false,
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  },
  "live-restore": true,
  "userland-proxy": false,
  "no-new-privileges": true
}
EOF
        systemctl restart docker
        log_success "Docker sécurisé"
    else
        log_warning "Docker non installé, configuration ignorée"
    fi
fi

# 11. Configuration Logrotate
log_info "11. Configuration Logrotate..."

cat > /etc/logrotate.d/simveb << 'EOF'
/opt/simveb/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 simveb simveb
}

/var/log/fail2ban.log {
    weekly
    rotate 4
    compress
    delaycompress
    missingok
    postrotate
        /usr/bin/fail2ban-client flushlogs >/dev/null
    endscript
}
EOF

log_success "Logrotate configuré"

# 12. Désactivation services inutiles
log_info "12. Désactivation des services inutiles..."

services_to_disable=(
    "bluetooth"
    "cups"
    "avahi-daemon"
)

for service in "${services_to_disable[@]}"; do
    if systemctl is-active --quiet "$service"; then
        systemctl stop "$service"
        systemctl disable "$service"
        log_info "Service $service désactivé"
    fi
done

log_success "Services inutiles désactivés"

# 13. Permissions fichiers sensibles
log_info "13. Sécurisation des permissions..."

chmod 600 /etc/ssh/sshd_config
chmod 644 /etc/passwd
chmod 640 /etc/shadow
chmod 640 /etc/group
chmod 600 /etc/gshadow

log_success "Permissions sécurisées"

# 14. Créer un rapport de sécurité
log_info "14. Génération du rapport de sécurité..."

REPORT_FILE="/root/security_report_$(date +%Y%m%d_%H%M%S).txt"

cat > "$REPORT_FILE" << EOF
========================================
SIMVEB SECURITY HARDENING REPORT
========================================
Date: $(date)
Hostname: $(hostname)
VM Type: $VM_TYPE
Environment: $ENVIRONMENT

CONFIGURATION APPLIED:
- SSH: Port 2222, clés seulement, PermitRootLogin=no
- Fail2Ban: Activé
- UFW: Activé
- Kernel: Sécurisé
- Mises à jour automatiques: Activées
- Auditd: Activé
- Logrotate: Configuré

SERVICES ACTIFS:
$(systemctl list-units --type=service --state=running | grep -E "(ssh|fail2ban|ufw|auditd|docker|postgresql)")

RÈGLES FIREWALL:
$(ufw status verbose)

UTILISATEURS:
$(cat /etc/passwd | grep -E "(simveb|postgres)")

TÂCHES MANUELLES RESTANTES:
1. Configurer les certificats SSL/TLS
2. Configurer PostgreSQL SSL (si VM DB)
3. Configurer les backups
4. Configurer le monitoring
5. Tester la connexion SSH avec la clé
6. Ajouter l'IP du serveur monitoring dans UFW

========================================
EOF

log_success "Rapport généré: $REPORT_FILE"

echo ""
log_success "========================================="
log_success "  HARDENING TERMINÉ !"
log_success "========================================="
echo ""
log_warning "IMPORTANT:"
log_warning "1. SSH est maintenant sur le port 2222"
log_warning "2. Seules les clés SSH sont autorisées"
log_warning "3. Testez la connexion SSH avant de fermer cette session!"
log_warning "4. Consultez le rapport: $REPORT_FILE"
log_warning "5. Complétez les tâches manuelles listées dans le rapport"
echo ""
log_info "Nouvelle connexion SSH: ssh -p 2222 -i ~/.ssh/votre_cle simveb@IP"
echo ""
