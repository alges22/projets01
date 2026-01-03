# Scripts de Déploiement SIMVEB

## Structure

```
deploy/
├── staging/
│   ├── docker-compose.yml    # Configuration Docker pour staging
│   └── deploy-all.sh         # Script de déploiement staging
├── production/
│   ├── docker-compose.yml    # Configuration Docker pour production
│   └── deploy-all.sh         # Script de déploiement production
├── database/
│   ├── init-db.sh            # Initialisation PostgreSQL
│   ├── backup-db.sh          # Backup base de données
│   └── restore-db.sh         # Restauration base de données
└── README.md                 # Ce fichier
```

## Utilisation

### Déploiement Staging

Le déploiement staging est **automatique** via GitLab CI/CD lors d'un push sur la branche `staging`.

Pour un déploiement manuel sur le serveur:

```bash
ssh simveb@10.x.x.10
cd /opt/simveb
bash deploy/staging/deploy-all.sh
```

### Déploiement Production

Le déploiement production est **manuel** et nécessite confirmation.

Via GitLab CI/CD (recommandé):
1. Push sur branche `main`
2. Aller dans GitLab > CI/CD > Pipelines
3. Cliquer sur "Play" pour le job `deploy:production`
4. Confirmer le déploiement

Pour un déploiement manuel sur le serveur:

```bash
ssh simveb@10.x.x.30
cd /opt/simveb
bash deploy/production/deploy-all.sh
```

Le script demandera une confirmation `yes` avant de procéder.

### Commandes Utiles

#### Déploiement

```bash
# Déployer (par défaut)
bash deploy/staging/deploy-all.sh
bash deploy/staging/deploy-all.sh deploy

# Voir le statut
bash deploy/staging/deploy-all.sh status

# Vérifier la santé
bash deploy/staging/deploy-all.sh health

# Rollback (production seulement)
bash deploy/production/deploy-all.sh rollback
```

#### Base de Données

```bash
# Initialiser la base de données
bash deploy/database/init-db.sh staging
bash deploy/database/init-db.sh production

# Créer un backup
bash deploy/database/backup-db.sh staging
bash deploy/database/backup-db.sh production

# Restaurer un backup
bash deploy/database/restore-db.sh staging /path/to/backup.sql.gz
bash deploy/database/restore-db.sh production /path/to/backup.sql.gz
```

#### Docker Compose

```bash
# Démarrer les services
cd /opt/simveb
docker-compose -f deploy/staging/docker-compose.yml up -d

# Arrêter les services
docker-compose -f deploy/staging/docker-compose.yml down

# Voir les logs
docker-compose -f deploy/staging/docker-compose.yml logs -f

# Redémarrer un service
docker-compose -f deploy/staging/docker-compose.yml restart backend
```

## Variables d'Environnement

### Fichiers .env

Les scripts attendent les fichiers suivants sur les serveurs:

- `/opt/simveb/.env.staging` - Variables staging
- `/opt/simveb/.env.production` - Variables production

Ces fichiers sont créés automatiquement par GitLab CI/CD lors du déploiement.

### Variables Requises

Voir `docs/SETUP_DEPLOYMENT.md` pour la liste complète des variables.

## Processus de Déploiement

### Staging

1. ✅ Vérification des prérequis
2. ✅ Chargement des variables
3. ✅ Login Docker Registry
4. ✅ Backup base de données
5. ✅ Pull des images Docker
6. ✅ Arrêt des conteneurs
7. ✅ Démarrage des nouveaux conteneurs
8. ✅ Migrations base de données
9. ✅ Optimisation Laravel
10. ✅ Health checks

### Production

Même processus que staging, avec:

- ⚠️ Confirmation manuelle obligatoire
- 🔒 Mode maintenance activé
- 💾 Backup base de données obligatoire
- 🏥 Health checks plus stricts
- 🔄 Rollback automatique en cas d'échec
- 📢 Notifications Slack

## Rollback

En production, le rollback restaure:

1. La version précédente des images Docker
2. La base de données (backup pré-déploiement)
3. Les conteneurs dans leur état précédent

```bash
# Via GitLab CI/CD
GitLab > Pipelines > Click "Play" sur rollback:production

# Manuellement
ssh simveb@10.x.x.30
cd /opt/simveb
bash deploy/production/deploy-all.sh rollback
```

## Monitoring

### Logs en Temps Réel

```bash
# Tous les conteneurs
docker-compose -f deploy/staging/docker-compose.yml logs -f

# Un service spécifique
docker logs -f simveb-backend-staging
docker logs -f simveb-portal-staging

# Logs Laravel
docker exec simveb-backend-staging tail -f storage/logs/laravel.log
```

### Statut des Services

```bash
# Liste des conteneurs
docker ps

# Statut détaillé
docker-compose -f deploy/staging/docker-compose.yml ps

# Statistiques
docker stats
```

### Health Checks

```bash
# Vérification manuelle
curl http://localhost:8080/health        # Backend
curl http://localhost:3000               # Portal
curl http://localhost:3001               # Backoffice
curl http://localhost:3002               # Affiliate

# Depuis le serveur
bash deploy/staging/deploy-all.sh health
```

## Sécurité

### Permissions

Les scripts doivent être exécutés par l'utilisateur `simveb`:

```bash
sudo chown -R simveb:simveb /opt/simveb
chmod +x deploy/staging/*.sh
chmod +x deploy/production/*.sh
chmod +x deploy/database/*.sh
```

### Secrets

⚠️ **Ne jamais committer les fichiers .env dans Git !**

Les secrets sont gérés via:
- GitLab CI/CD Variables (pour les déploiements automatiques)
- Fichiers .env sur les serveurs (créés par CI/CD)

### Accès SSH

Seules les clés SSH autorisées peuvent se connecter:

```bash
# Ajouter une clé
echo "ssh-ed25519 AAAA..." >> ~/.ssh/authorized_keys

# Lister les clés
cat ~/.ssh/authorized_keys
```

## Troubleshooting

### Le script échoue

```bash
# Vérifier les logs du script
bash deploy/staging/deploy-all.sh 2>&1 | tee deploy.log

# Vérifier Docker
docker info
docker-compose version

# Vérifier les variables
cat /opt/simveb/.env.staging
```

### Les conteneurs ne démarrent pas

```bash
# Voir les erreurs
docker-compose -f deploy/staging/docker-compose.yml logs

# Recréer les conteneurs
docker-compose -f deploy/staging/docker-compose.yml down -v
docker-compose -f deploy/staging/docker-compose.yml up -d
```

### Erreur de connexion DB

```bash
# Tester la connexion
psql -h $DB_HOST -U $DB_USERNAME -d simveb_staging

# Vérifier les variables
docker exec simveb-backend-staging env | grep DB_
```

## Maintenance

### Nettoyage

```bash
# Nettoyer les images inutilisées
docker system prune -a -f

# Nettoyer les vieux logs
find /opt/simveb/logs -type f -mtime +30 -delete

# Nettoyer les vieux backups (garde les 30 derniers)
cd /opt/simveb/backups
ls -t *.sql.gz | tail -n +31 | xargs rm -f
```

### Mise à Jour

Les mises à jour se font automatiquement via GitLab CI/CD:

1. Push du code sur `staging` ou `main`
2. Le pipeline build les nouvelles images
3. Le déploiement pull les nouvelles images
4. Les conteneurs sont recréés avec les nouvelles images

## Support

- 📖 Documentation complète: `/docs/SETUP_DEPLOYMENT.md`
- 🐛 Issues GitLab: `https://gitlab.com/your-org/simveb/issues`
- 💬 Équipe DevOps: `devops@simveb-bj.com`

---

**Version:** 1.0
**Dernière mise à jour:** 2026-01-03
