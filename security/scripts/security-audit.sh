#!/bin/bash

# ========================================
# SIMVEB - Script d'Audit Sécurité
# Vérifie la configuration sécurité
# ========================================

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PASS=0
FAIL=0
WARN=0

check_pass() {
    echo -e "${GREEN}[PASS]${NC} $1"
    ((PASS++))
}

check_fail() {
    echo -e "${RED}[FAIL]${NC} $1"
    ((FAIL++))
}

check_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
    ((WARN++))
}

echo ""
echo "========================================="
echo "  SIMVEB SECURITY AUDIT"
echo "  Date: $(date)"
echo "  Hostname: $(hostname)"
echo "========================================="
echo ""

# 1. SSH Configuration
echo "1. CONFIGURATION SSH"
echo "-------------------"

if grep -q "^PermitRootLogin no" /etc/ssh/sshd_config 2>/dev/null; then
    check_pass "PermitRootLogin est désactivé"
else
    check_fail "PermitRootLogin n'est pas désactivé"
fi

if grep -q "^PasswordAuthentication no" /etc/ssh/sshd_config 2>/dev/null; then
    check_pass "Authentification par mot de passe désactivée"
else
    check_fail "Authentification par mot de passe encore activée"
fi

if grep -q "^Port 2222" /etc/ssh/sshd_config 2>/dev/null; then
    check_pass "SSH sur port non-standard (2222)"
else
    check_warn "SSH sur port par défaut (22)"
fi

echo ""

# 2. Firewall
echo "2. FIREWALL"
echo "-----------"

if systemctl is-active --quiet ufw; then
    check_pass "UFW est actif"

    if ufw status | grep -q "Status: active"; then
        check_pass "UFW est enabled"
    else
        check_fail "UFW n'est pas enabled"
    fi
else
    check_fail "UFW n'est pas actif"
fi

echo ""

# 3. Fail2Ban
echo "3. FAIL2BAN"
echo "-----------"

if systemctl is-active --quiet fail2ban; then
    check_pass "Fail2Ban est actif"

    jails=$(fail2ban-client status | grep "Jail list" | sed 's/.*://;s/,//g')
    if [ -n "$jails" ]; then
        check_pass "Jails Fail2Ban: $jails"
    else
        check_warn "Aucune jail Fail2Ban configurée"
    fi
else
    check_fail "Fail2Ban n'est pas actif"
fi

echo ""

# 4. Mises à jour
echo "4. MISES À JOUR"
echo "---------------"

if dpkg -l | grep -q unattended-upgrades; then
    check_pass "Mises à jour automatiques installées"
else
    check_fail "Mises à jour automatiques non installées"
fi

# Vérifier les packages à mettre à jour
updates=$(apt list --upgradable 2>/dev/null | grep -c upgradable)
if [ "$updates" -eq 0 ]; then
    check_pass "Système à jour"
else
    check_warn "$updates mises à jour disponibles"
fi

echo ""

# 5. Auditd
echo "5. AUDITD"
echo "---------"

if systemctl is-active --quiet auditd; then
    check_pass "Auditd est actif"

    rules=$(auditctl -l | wc -l)
    if [ "$rules" -gt 5 ]; then
        check_pass "$rules règles audit configurées"
    else
        check_warn "Peu de règles audit ($rules)"
    fi
else
    check_fail "Auditd n'est pas actif"
fi

echo ""

# 6. Docker (si présent)
echo "6. DOCKER SECURITY"
echo "------------------"

if command -v docker &> /dev/null; then
    check_pass "Docker installé"

    if [ -f /etc/docker/daemon.json ]; then
        check_pass "Configuration Docker daemon.json présente"

        if grep -q "no-new-privileges" /etc/docker/daemon.json; then
            check_pass "no-new-privileges configuré"
        else
            check_warn "no-new-privileges non configuré"
        fi
    else
        check_warn "Fichier daemon.json manquant"
    fi

    # Vérifier les conteneurs en root
    root_containers=$(docker ps -q 2>/dev/null | xargs -r docker inspect --format '{{.Config.User}}:{{.Name}}' | grep -c "^:" || true)
    if [ "$root_containers" -gt 0 ]; then
        check_warn "$root_containers conteneur(s) en root"
    else
        check_pass "Aucun conteneur en root"
    fi
else
    echo "  Docker non installé (ignoré)"
fi

echo ""

# 7. PostgreSQL (si présent)
echo "7. POSTGRESQL SECURITY"
echo "----------------------"

if systemctl is-active --quiet postgresql 2>/dev/null; then
    check_pass "PostgreSQL actif"

    # Vérifier SSL
    if sudo -u postgres psql -t -c "SHOW ssl;" 2>/dev/null | grep -q "on"; then
        check_pass "SSL PostgreSQL activé"
    else
        check_fail "SSL PostgreSQL désactivé"
    fi

    # Vérifier password encryption
    encryption=$(sudo -u postgres psql -t -c "SHOW password_encryption;" 2>/dev/null | xargs)
    if [ "$encryption" = "scram-sha-256" ]; then
        check_pass "Password encryption: scram-sha-256"
    else
        check_warn "Password encryption: $encryption (recommandé: scram-sha-256)"
    fi
else
    echo "  PostgreSQL non actif (ignoré)"
fi

echo ""

# 8. Permissions fichiers sensibles
echo "8. PERMISSIONS FICHIERS"
echo "-----------------------"

check_file_perm() {
    file=$1
    expected=$2

    if [ -f "$file" ]; then
        perm=$(stat -c %a "$file")
        if [ "$perm" = "$expected" ] || [ "$perm" -le "$expected" ]; then
            check_pass "$file ($perm)"
        else
            check_fail "$file ($perm, attendu: $expected)"
        fi
    fi
}

check_file_perm "/etc/shadow" "640"
check_file_perm "/etc/gshadow" "640"
check_file_perm "/etc/ssh/sshd_config" "600"

echo ""

# 9. Services inutiles
echo "9. SERVICES INUTILES"
echo "--------------------"

unwanted_services=("bluetooth" "cups" "avahi-daemon")
for service in "${unwanted_services[@]}"; do
    if systemctl is-active --quiet "$service" 2>/dev/null; then
        check_warn "Service inutile actif: $service"
    fi
done

if [ $WARN -eq 0 ]; then
    check_pass "Aucun service inutile actif"
fi

echo ""

# 10. Kernel parameters
echo "10. KERNEL PARAMETERS"
echo "---------------------"

check_sysctl() {
    param=$1
    expected=$2

    value=$(sysctl -n "$param" 2>/dev/null)
    if [ "$value" = "$expected" ]; then
        check_pass "$param = $expected"
    else
        check_warn "$param = $value (attendu: $expected)"
    fi
}

check_sysctl "net.ipv4.tcp_syncookies" "1"
check_sysctl "net.ipv4.conf.all.accept_redirects" "0"
check_sysctl "net.ipv4.conf.all.send_redirects" "0"

echo ""

# 11. Utilisateurs et groupes
echo "11. UTILISATEURS ET GROUPES"
echo "---------------------------"

# Vérifier utilisateurs avec UID 0
uid0=$(awk -F: '$3 == 0 {print $1}' /etc/passwd | grep -v "^root$")
if [ -z "$uid0" ]; then
    check_pass "Seul root a UID 0"
else
    check_fail "Autres utilisateurs avec UID 0: $uid0"
fi

# Vérifier utilisateurs sans mot de passe
no_pass=$(awk -F: '$2 == "" {print $1}' /etc/shadow 2>/dev/null)
if [ -z "$no_pass" ]; then
    check_pass "Aucun utilisateur sans mot de passe"
else
    check_fail "Utilisateurs sans mot de passe: $no_pass"
fi

echo ""

# 12. Logs
echo "12. LOGS ET MONITORING"
echo "----------------------"

if [ -d /var/log/audit ]; then
    audit_size=$(du -sh /var/log/audit 2>/dev/null | cut -f1)
    check_pass "Logs audit présents ($audit_size)"
else
    check_warn "Répertoire audit logs manquant"
fi

if [ -f /etc/logrotate.d/simveb ]; then
    check_pass "Logrotate SIMVEB configuré"
else
    check_warn "Logrotate SIMVEB non configuré"
fi

echo ""

# 13. Disque et ressources
echo "13. RESSOURCES SYSTÈME"
echo "----------------------"

disk_usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ "$disk_usage" -lt 80 ]; then
    check_pass "Espace disque: ${disk_usage}%"
else
    check_warn "Espace disque élevé: ${disk_usage}%"
fi

load=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
check_pass "Load average: $load"

echo ""

# RÉSUMÉ
echo "========================================="
echo "  RÉSUMÉ DE L'AUDIT"
echo "========================================="
echo -e "${GREEN}PASS: $PASS${NC}"
echo -e "${YELLOW}WARN: $WARN${NC}"
echo -e "${RED}FAIL: $FAIL${NC}"
echo ""

total=$((PASS + WARN + FAIL))
score=$((PASS * 100 / total))

if [ $score -ge 90 ]; then
    echo -e "${GREEN}Score de sécurité: $score% - EXCELLENT${NC}"
elif [ $score -ge 70 ]; then
    echo -e "${YELLOW}Score de sécurité: $score% - BON${NC}"
elif [ $score -ge 50 ]; then
    echo -e "${YELLOW}Score de sécurité: $score% - MOYEN${NC}"
else
    echo -e "${RED}Score de sécurité: $score% - FAIBLE${NC}"
fi

echo ""

if [ $FAIL -gt 0 ]; then
    echo -e "${RED}⚠️  Des problèmes critiques ont été détectés!${NC}"
    echo "Consultez security/SECURITY_GUIDE.md pour les corriger"
    exit 1
else
    echo -e "${GREEN}✅ Aucun problème critique détecté${NC}"
    if [ $WARN -gt 0 ]; then
        echo -e "${YELLOW}⚠️  $WARN avertissement(s) à examiner${NC}"
    fi
    exit 0
fi
