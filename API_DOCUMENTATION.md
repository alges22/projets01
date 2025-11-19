# Documentation Complète des API - Projet SIMVEB

Ce document répertorie **toutes les API** utilisées dans le projet SIMVEB (Backend, Portal, Backoffice, Affiliate).

---

## 📋 Table des Matières

1. [API Backend (Routes REST)](#api-backend-routes-rest)
2. [API Tierces (Services Externes)](#api-tierces-services-externes)
3. [API Consommées par les Frontends](#api-consommées-par-les-frontends)
4. [Configuration des Clients HTTP](#configuration-des-clients-http)

---

## 1. API Backend (Routes REST)

Le backend Laravel expose **plus de 400 endpoints API** organisés par domaine fonctionnel.

### 1.1 Authentification & Session

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| POST | `/login/send-otp` | Envoyer OTP pour connexion | Non |
| POST | `/login/resend-otp` | Renvoyer OTP | Non |
| POST | `/login` | Vérifier OTP et se connecter | Non |
| POST | `/logout` | Déconnexion | Oui |
| GET | `/current-user` | Obtenir l'utilisateur actuel | Oui |
| PUT | `/update-password` | Modifier le mot de passe | Oui |
| PUT | `/change-space` | Changer d'espace de travail | Oui |
| POST | `/forgot-password` | Initier réinitialisation | Non |
| POST | `/reset-password` | Réinitialiser mot de passe | Non |

### 1.2 Inscription

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| POST | `/register/init` | Initialiser inscription | Non |
| POST | `/register/resend-otp` | Renvoyer OTP d'inscription | Non |
| POST | `/register/check-otp` | Vérifier OTP | Non |
| GET | `/register/space-documents` | Documents requis par espace | Non |
| POST | `/register/store` | Compléter inscription | Non |

### 1.3 Gestion des Utilisateurs

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/staff` | Liste du personnel | Oui |
| POST | `/staff` | Créer un membre du personnel | Oui |
| GET | `/staff/{id}` | Détails d'un membre | Oui |
| PUT | `/staff/{id}` | Modifier un membre | Oui |
| DELETE | `/staff/{id}` | Supprimer un membre | Oui |
| GET | `/user-details/{npi}` | Détails utilisateur par NPI | Oui |

### 1.4 Profils & Rôles

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/profiles` | Liste des profils | Oui |
| GET | `/profiles/{id}` | Détails d'un profil | Oui |
| PUT | `/update-profile/{id}` | Modifier un profil | Oui |
| GET | `/profiles/search` | Rechercher des profils | Oui |
| GET | `/roles` | Liste des rôles | Oui |
| POST | `/roles` | Créer un rôle | Oui |
| PUT | `/roles/{id}` | Modifier un rôle | Oui |
| DELETE | `/roles/{id}` | Supprimer un rôle | Oui |
| GET | `/permissions` | Liste des permissions | Oui |

### 1.5 Gestion des Véhicules

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/vehicles` | Liste des véhicules | Oui |
| POST | `/vehicles` | Créer un véhicule | Oui |
| GET | `/vehicles/create` | Données pour création | Oui |
| GET | `/vehicles/{id}` | Détails d'un véhicule | Oui |
| PUT | `/vehicles/{id}` | Modifier un véhicule | Oui |
| DELETE | `/vehicles/{id}` | Supprimer un véhicule | Oui |
| GET | `/vehicles/{id}/plates` | Plaques d'un véhicule | Oui |
| GET | `/get-vehicle` | Obtenir véhicule par VIN | Oui |
| POST | `/store-vehicle-by-vin` | Enregistrer par VIN | Oui |

### 1.6 Propriétaires de Véhicules

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/vehicle-owners` | Liste des propriétaires | Oui |
| POST | `/vehicle-owners` | Créer un propriétaire | Oui |
| GET | `/vehicle-owners/{id}` | Détails propriétaire | Oui |
| PUT | `/vehicle-owners/{id}` | Modifier propriétaire | Oui |
| DELETE | `/vehicle-owners/{id}` | Supprimer propriétaire | Oui |
| POST | `/vehicle-owners-info` | Info propriétaire | Non |
| POST | `/vehicle-owners-subscribe` | Abonner propriétaire | Non |

### 1.7 Configuration Véhicules

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET/POST | `/vehicle-types` | Types de véhicules | Oui |
| GET/POST | `/vehicle-categories` | Catégories de véhicules | Oui |
| GET/POST | `/vehicle-brands` | Marques de véhicules | Oui |
| GET/POST | `/vehicle-energy-sources` | Sources d'énergie | Oui |
| GET/POST | `/vehicle-powers` | Puissances | Oui |
| GET/POST | `/vehicle-characteristic-categories` | Catégories caractéristiques | Oui |
| GET/POST | `/vehicle-characteristics` | Caractéristiques | Oui |

### 1.8 Passages & Alertes Véhicules

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/vehicle-passages` | Liste des passages | Oui |
| POST | `/vehicle-passages` | Créer un passage | Oui |
| POST | `/vehicle-passages/get-vehicle-infos` | Info véhicule | Oui |
| GET | `/vehicle-passages/vehicle-history/{immat}` | Historique véhicule | Oui |
| GET | `/vehicle-alerts` | Liste des alertes | Oui |
| POST | `/vehicle-alerts` | Créer une alerte | Oui |

### 1.9 Véhicules en Liste Noire

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/blacklist-vehicles` | Liste noire véhicules | Oui |
| POST | `/blacklist-vehicles` | Ajouter à la liste noire | Oui |
| GET | `/blacklist-vehicles/{id}` | Détails véhicule | Oui |
| DELETE | `/blacklist-vehicles/{id}` | Retirer de la liste | Oui |
| GET | `/black-vehicles/file-format` | Format import | Oui |
| POST | `/black-vehicles/import` | Importer liste noire | Oui |
| PUT | `/blacklist-vehicles/{id}/validate` | Valider | Oui |
| PUT | `/blacklist-vehicles/{id}/reject` | Rejeter | Oui |

### 1.10 Immatriculation

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/immatriculations` | Liste immatriculations | Oui |
| POST | `/immatriculations` | Créer immatriculation | Oui |
| GET | `/immatriculations/{id}` | Détails immatriculation | Oui |
| PUT | `/immatriculations/{id}` | Modifier immatriculation | Oui |
| DELETE | `/immatriculations/{id}` | Supprimer immatriculation | Oui |
| GET | `/generate-immatriculation-number` | Générer numéro | Oui |
| GET/POST | `/immatriculation-formats` | Formats immatriculation | Oui |
| GET/POST | `/immatriculation-types` | Types immatriculation | Oui |
| GET | `/immatriculation-label` | Étiquette immatriculation | Oui |

### 1.11 Labels Prestige

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/prestige-label-immatriculations` | Liste labels prestige | Oui |
| POST | `/prestige-label-immatriculations` | Créer label prestige | Oui |
| POST | `/submit-prestige-label-immatriculations` | Soumettre label | Oui |

### 1.12 Demandes d'Immatriculation

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/immatriculation-demands/create` | Données création | Non |
| POST | `/immatriculation-demands` | Créer demande | Non |
| GET | `/immatriculation-demands` | Liste demandes | Oui |
| GET | `/immatriculation-demands/{id}` | Détails demande | Oui |
| PUT | `/immatriculation-demands/{id}` | Modifier demande | Oui |
| DELETE | `/immatriculation-demands/{id}` | Supprimer demande | Oui |
| POST | `/anatt-control-immatriculation-demand` | Contrôle ANATT | Oui |

### 1.13 Demandes Admin

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/admin-demands` | Liste demandes admin | Oui |
| GET | `/my-pending-demands` | Mes demandes en attente | Oui |
| GET | `/my-treated-demands` | Mes demandes traitées | Oui |
| GET | `/interpol-demands` | Demandes Interpol | Oui |
| GET | `/demands/{id}` | Détails demande | Oui |
| PUT | `/demands/{id}/validate-updates` | Valider modifications | Oui |
| POST | `/demand-updates/validate` | Valider toutes mises à jour | Oui |
| POST | `/add-demand-to-cart/{id}` | Ajouter au panier | Non |

### 1.14 Commandes

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/admin-orders` | Liste commandes admin | Oui |
| GET | `/admin-orders/{id}` | Détails commande | Oui |
| POST | `/invoices/{order}/generate` | Générer facture | Oui |

### 1.15 Exports

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/exports/excel/demands` | Export demandes Excel | Oui |
| GET | `/exports/pdf/demands` | Export demandes PDF | Oui |
| GET | `/exports/excel/orders` | Export commandes Excel | Oui |
| GET | `/exports/pdf/orders` | Export commandes PDF | Oui |

### 1.16 Traitement & Workflow

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/treatments/create` | Données création traitement | Oui |
| POST | `/assign-demand-to-center` | Affecter au centre | Oui |
| POST | `/assign-demand-to-service` | Affecter au service | Oui |
| POST | `/assign-demand-to-interpol` | Affecter à Interpol | Oui |
| POST | `/assign-demand-to-staff` | Affecter au personnel | Oui |
| POST | `/verify-demand` | Vérifier demande | Oui |
| POST | `/reject-demand` | Rejeter demande | Oui |
| POST | `/validate-demand` | Valider demande | Oui |
| POST | `/validate-demand-interpol` | Valider par Interpol | Oui |
| POST | `/suspend-demand` | Suspendre demande | Oui |
| POST | `/close-demand` | Fermer demande | Oui |
| POST | `/print-or-emit-order` | Imprimer/émettre | Oui |
| POST | `/emit-print-order` | Émettre ordre impression | Oui |

### 1.17 Transformations de Plaques

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/plate-transformations` | Liste transformations | Oui |
| POST | `/plate-transformations` | Créer transformation | Oui |
| POST | `/submit-plate-transformations` | Soumettre transformation | Oui |

### 1.18 Portefeuille

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/wallets/details` | Détails portefeuille | Oui |
| GET | `/wallets/transactions` | Transactions portefeuille | Oui |
| POST | `/wallets/recharge` | Recharger portefeuille | Oui |

### 1.19 Gages (Pledges)

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/pledge` | Liste des gages | Oui |
| POST | `/pledge` | Créer un gage | Oui |
| GET | `/pledge/{id}` | Détails gage | Oui |
| PUT | `/pledge/{id}` | Modifier gage | Oui |
| DELETE | `/pledge/{id}` | Supprimer gage | Oui |
| GET | `/pledge/vehicle/owner` | Véhicule & propriétaire par VIN | Oui |
| GET | `/pledge/clerk/court` | Greffier par tribunal | Oui |
| PUT | `/pledge/affectation/{id}` | Affecter au greffier | Oui |
| PUT | `/pledge/validate/{id}` | Valider gage | Oui |
| PUT | `/pledge/reject/{id}` | Rejeter gage | Oui |
| PUT | `/pledge/lift/{id}` | Lever gage | Oui |
| GET | `/pledge/liftable/list` | Gages levables | Oui |

### 1.20 Levée de Gage

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/pledge-lift` | Liste levées de gage | Oui |
| GET | `/pledge-lift/{id}` | Détails levée | Oui |
| PUT | `/pledge-lift/{id}` | Modifier levée | Oui |
| DELETE | `/pledge-lift/{id}` | Supprimer levée | Oui |
| PUT | `/pledge-lift/reject/{id}` | Rejeter levée | Oui |
| PUT | `/pledge-lift/validate/{id}` | Valider levée | Oui |

### 1.21 Oppositions

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/oppositions` | Liste oppositions | Oui |
| POST | `/oppositions` | Créer opposition | Oui |
| GET | `/oppositions/{id}` | Détails opposition | Oui |
| PUT | `/oppositions/{id}` | Modifier opposition | Oui |
| DELETE | `/oppositions/{id}` | Supprimer opposition | Oui |
| GET | `/owner/vehicles` | Véhicules par NPI/IFU | Oui |
| PUT | `/opposition/validate/{id}` | Valider opposition | Oui |
| PUT | `/opposition/reject/{id}` | Rejeter opposition | Oui |
| PUT | `/opposition/lift/{id}` | Lever opposition | Oui |

### 1.22 Gestion des Espaces

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/spaces` | Liste des espaces | Oui |
| GET | `/spaces/{id}` | Détails espace | Oui |
| PUT | `/spaces/{id}` | Modifier espace | Oui |
| DELETE | `/spaces/{id}` | Supprimer espace | Oui |
| GET | `/spaces/members` | Membres de l'espace | Oui |
| GET | `/spaces/details` | Détails de l'espace | Oui |

### 1.23 Inscriptions d'Espaces

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/space-registration-requests` | Demandes inscription | Oui |
| POST | `/space-registration-requests` | Créer demande | Oui |
| GET | `/space-registration-requests/create` | Données création | Oui |
| GET | `/space-registration-requests/{id}` | Détails demande | Oui |
| POST | `/space-registration-requests/validate/{id}` | Valider inscription | Oui |
| POST | `/space-registration-requests/reject/{id}` | Rejeter inscription | Oui |

### 1.24 Suspension d'Espaces

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/space-suspension-requests` | Demandes suspension | Oui |
| POST | `/space-suspension-requests` | Créer demande | Oui |
| GET | `/space-suspension-requests/{id}` | Détails demande | Oui |
| PUT | `/space-suspension-requests/{id}` | Modifier demande | Oui |
| PUT | `/space-suspension-requests/{id}/validate-or-reject` | Valider/Rejeter | Oui |

### 1.25 Levée de Suspension

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/space-suspension-lifting-requests` | Demandes levée | Oui |
| POST | `/space-suspension-lifting-requests` | Créer demande | Oui |
| GET | `/space-suspension-lifting-requests/{id}` | Détails | Oui |
| PUT | `/space-suspension-lifting-requests/{id}` | Modifier | Oui |
| PUT | `/space-suspension-lifting-requests/{id}/validate-or-reject` | Valider/Rejeter | Oui |

### 1.26 Organisations & Institutions

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET/POST | `/organizations` | Organisations | Oui |
| GET/PUT/DELETE | `/organizations/{id}` | Gestion organisation | Oui |
| GET/POST | `/institutions` | Institutions | Oui |
| GET/POST | `/institution-types` | Types institutions | Oui |

### 1.27 Services

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/services` | Liste services | Oui |
| POST | `/services` | Créer service | Oui |
| GET | `/services/{id}` | Détails service | Oui |
| PUT | `/services/{id}` | Modifier service | Oui |
| DELETE | `/services/{id}` | Supprimer service | Oui |
| PUT | `/toggle-service/{id}` | Activer/Désactiver | Oui |
| GET/POST | `/service-types` | Types de services | Oui |

### 1.28 Plaques

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/plates` | Liste plaques | Oui |
| POST | `/plates` | Créer plaque | Oui |
| GET | `/plates/stats` | Statistiques plaques | Oui |
| GET/POST | `/plate-colors` | Couleurs plaques | Oui |
| GET/POST | `/plate-shapes` | Formes plaques | Oui |

### 1.29 Commandes de Plaques

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/plate-orders` | Liste commandes | Oui |
| POST | `/plate-orders` | Créer commande | Oui |
| GET | `/plate-orders/{id}` | Détails commande | Oui |
| GET | `/plate-orders/invoices` | Factures | Oui |
| GET | `/plate-orders/requests` | Demandes | Oui |
| POST | `/plate-orders/generate-invoice-file/{id}` | Générer facture | Oui |
| POST | `/plate-orders/confirm` | Confirmer commande | Oui |
| POST | `/plate-orders/reject` | Rejeter commande | Oui |
| POST | `/plate-orders/pay` | Payer commande | Oui |
| POST | `/plate-orders/validate` | Valider commande | Oui |

### 1.30 Ordres d'Impression

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/print-orders` | Liste ordres | Oui |
| GET | `/print-orders/{id}` | Détails ordre | Oui |
| GET | `/print-orders/search` | Rechercher ordres | Oui |
| POST | `/print-orders/confirm-affectation` | Confirmer affectation | Oui |
| POST | `/print-orders/print-plate` | Imprimer plaque | Oui |
| POST | `/print-orders/print-gray-card` | Imprimer carte grise | Oui |
| POST | `/print-orders/validate-or-reject` | Valider/Rejeter | Oui |

### 1.31 Demandes d'Impression

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/impression-demands` | Liste demandes | Oui |
| POST | `/impression-demands` | Créer demande | Oui |
| GET | `/impression-demands/{id}` | Détails demande | Oui |
| POST | `/impression-demands/init` | Initialiser demande | Oui |
| POST | `/impression-demands/reject` | Rejeter demande | Oui |
| GET | `/impression-demands/validation-create/{id}` | Données validation | Oui |
| POST | `/impression-demands/validate` | Valider demande | Oui |
| POST | `/impression-demands/confirm` | Confirmer demande | Oui |
| POST | `/impression-demands/confirm-reception/{id}` | Confirmer réception | Oui |

### 1.32 Zones Géographiques

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET/POST | `/geographical-areas` | Zones géographiques | Oui |
| GET | `/geographical-areas-staff/{id}` | Personnel par zone | Oui |
| GET/POST | `/towns` | Villes | Oui |
| GET | `/get-districts-for-town` | Districts par ville | Oui |
| GET/POST | `/districts` | Districts | Oui |
| GET | `/get-villages-for-district` | Villages par district | Oui |
| GET/POST | `/villages` | Villages | Oui |
| GET/POST | `/zones` | Zones | Oui |

### 1.33 Configuration Diverses

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET/POST | `/legal-statuses` | Statuts juridiques | Oui |
| GET/POST | `/owner-types` | Types propriétaires | Oui |
| GET/POST | `/parks` | Parcs | Oui |
| GET/POST | `/borders` | Frontières | Oui |
| GET/POST | `/management-center-types` | Types centres gestion | Oui |
| GET/POST | `/management-centers` | Centres de gestion | Oui |
| GET/POST | `/prices` | Prix | Oui |
| GET/POST | `/number-templates` | Modèles numéros | Oui |
| GET/POST | `/alert-types` | Types alertes | Oui |
| GET/POST | `/actions` | Actions | Oui |
| GET/POST | `/title-reasons` | Raisons de titre | Oui |
| GET/POST | `/title-reason-types` | Types raisons titre | Oui |
| GET/POST | `/transformation-types` | Types transformations | Oui |
| GET/PUT | `/reimmatriculation-reasons` | Raisons réimmatriculation | Oui |

### 1.34 Numéros de Plaques Réservés

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/reserved-plate-numbers` | Numéros réservés | Oui |
| POST | `/reserved-plate-numbers` | Créer réservation | Oui |
| POST | `/reserved-plate-numbers/validate-or-invalidate` | Valider/Invalider | Oui |

### 1.35 Personnes en Liste Noire

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/blacklist-persons` | Liste noire personnes | Oui |
| POST | `/blacklist-persons` | Ajouter personne | Oui |
| GET | `/black-persons/file-format` | Format import | Oui |
| POST | `/black-persons/import` | Importer liste | Oui |

### 1.36 Commissions

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/commissions` | Liste commissions | Oui |
| POST | `/commissions` | Créer commission | Oui |
| GET | `/commissions/{id}` | Détails commission | Oui |
| PUT | `/commissions/{id}` | Modifier commission | Oui |
| DELETE | `/commissions/{id}` | Supprimer commission | Oui |

### 1.37 Configuration Notifications

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/notification-configs` | Configurations notifications | Oui |
| GET | `/notification-configs/{id}` | Détails config | Oui |
| PUT | `/notification-configs/{id}` | Modifier config | Oui |

### 1.38 Fournisseurs de Paiement

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/payment-providers` | Fournisseurs paiement | Oui |
| GET | `/payment-providers/{id}` | Détails fournisseur | Oui |
| PUT | `/payment-providers/{id}` | Modifier fournisseur | Oui |
| GET | `/payment-providers/active` | Fournisseurs actifs | Oui |
| PUT | `/payment-providers/{id}/toggle` | Activer/Désactiver | Oui |
| PUT | `/payment-providers/{id}/default` | Définir par défaut | Oui |

### 1.39 Statuts Administratifs Véhicules

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/vehicle-administrative-status` | Statuts administratifs | Oui |
| POST | `/vehicle-administrative-status` | Créer statut | Oui |
| GET | `/vehicle-administrative-status/{id}` | Détails statut | Oui |
| PUT | `/vehicle-administrative-status/{id}` | Modifier statut | Oui |
| DELETE | `/vehicle-administrative-status/{id}` | Supprimer statut | Oui |

### 1.40 Autorisations Vitres Teintées

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/tinted-windows-authorizations` | Autorisations | Oui |
| POST | `/tinted-windows-authorizations` | Créer autorisation | Oui |
| GET | `/tinted-windows-authorizations-expiry-status/{id}` | Statut expiration | Oui |
| PUT | `/tinted-windows-authorizations-expiry-status/{id}` | Modifier expiration | Oui |

### 1.41 Documents d'Immatriculation Internationale

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/intl-vehicle-reg-docs` | Documents internationaux | Oui |
| POST | `/intl-vehicle-reg-docs` | Créer document | Oui |
| GET | `/intl-vehicle-reg-docs-expiry-status/{id}` | Statut expiration | Oui |
| PUT | `/intl-vehicle-reg-docs-expiry-status/{id}` | Modifier expiration | Oui |

### 1.42 Déclarants

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/declarants` | Liste déclarants | Oui |
| POST | `/declarants` | Créer déclarant | Oui |
| GET | `/declarants/{id}` | Détails déclarant | Oui |
| PUT | `/declarants/{id}` | Modifier déclarant | Oui |
| DELETE | `/declarants/{id}` | Supprimer déclarant | Oui |

### 1.43 Invitations

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/invitations` | Liste invitations | Oui |
| POST | `/invitations` | Créer invitation | Oui |
| GET | `/invitations/{id}` | Détails invitation | Oui |
| POST | `/invitations/{id}/resend` | Renvoyer invitation | Oui |
| PUT | `/invitations/{id}/validate` | Valider invitation | Oui |
| PUT | `/invitations/{id}/deny` | Refuser invitation | Oui |

### 1.44 Accréditations

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/accreditations` | Liste accréditations | Oui |
| POST | `/accreditations` | Créer accréditation | Oui |
| GET | `/accreditations/{id}` | Détails accréditation | Oui |
| PUT | `/accreditations/{id}` | Modifier accréditation | Oui |
| DELETE | `/accreditations/{id}` | Supprimer accréditation | Oui |
| GET | `/accreditations/user/search` | Rechercher profils | Oui |
| POST | `/accreditations/validate` | Valider accréditation | Oui |
| POST | `/accreditations/reject` | Rejeter accréditation | Oui |

### 1.45 Affectations Policiers

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/police-officers/assignments` | Affectations | Oui |
| POST | `/police-officers/assignments` | Créer affectation | Oui |
| GET | `/police-officers/assignments/{id}` | Détails affectation | Oui |
| PUT | `/police-officers/assignments/{id}` | Modifier affectation | Oui |
| DELETE | `/police-officers/assignments/{id}` | Supprimer affectation | Oui |
| POST | `/police-officers/assignments/validate` | Valider affectation | Oui |
| POST | `/police-officers/assignments/reject` | Rejeter affectation | Oui |

### 1.46 Services Identité

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/get-identity/{npi}` | Obtenir identité par NPI | Oui |
| GET | `/get-identities/{npis}` | Obtenir identités multiples | Oui |
| GET | `/get-company/{ifu}` | Obtenir entreprise par IFU | Oui |

### 1.47 Import/Export

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/export/{modelName}` | Exporter vers Excel | Oui |
| POST | `/import/{modelName}` | Importer depuis Excel | Oui |

### 1.48 Statistiques

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/dashboard-stats` | Stats tableau de bord | Oui |
| GET | `/stats/imm` | Stats immatriculation | Oui |
| GET | `/stats/demands/total` | Total demandes | Oui |
| GET | `/stats/demands/total-by-vehicle-category` | Demandes par catégorie | Oui |
| GET | `/stats/demands/total-by-service` | Demandes par service | Oui |
| GET | `/stats/orders` | Stats commandes | Oui |
| GET | `/stats/transactions/total-amount` | Montant total transactions | Oui |
| GET | `/stats/services/popular` | Services populaires | Oui |
| GET | `/stats/services/unpopular` | Services impopulaires | Oui |
| GET | `/stats/plates/total` | Total plaques | Oui |
| GET | `/stats/profiles/total-by-profile-types` | Profils par type | Oui |
| GET | `/pledge-stats/total-pledges` | Total gages | Oui |
| GET | `/pledge-stats/active-closed-pledges` | Gages actifs/fermés | Oui |
| GET | `/opposition-stats/total` | Total oppositions | Oui |
| GET | `/opposition-stats/active-closed-opposition` | Oppositions actives/fermées | Oui |

### 1.49 Endpoints Portal (Préfixe `/portal`)

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/portal/service-types` | Types de services | Non |
| GET | `/portal/services` | Services disponibles | Non |
| GET | `/portal/services/{id}` | Détails service | Non |
| POST | `/portal/transactions` | Créer transaction | Non |
| GET | `/portal/immatriculation-search` | Rechercher immatriculation | Non |
| GET | `/portal/demand/{id}` | Détails demande | Non |
| GET | `/portal/immatriculation/{id}` | Détails immatriculation | Non |
| GET | `/portal/search-declarant` | Rechercher déclarant | Non |
| GET | `/portal/search-vehicle` | Rechercher véhicule | Non |
| POST | `/portal/vehicle-administrative-status` | Créer statut admin | Non |
| POST | `/portal/prestige-label-immatriculation-demand` | Demande label prestige | Non |
| POST | `/portal/plate-transformation-demand` | Demande transformation plaque | Non |

### 1.50 Endpoints Client (Préfixe `/client`)

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/client/services` | Services client | Non |
| GET | `/client/services/{code}` | Service par code | Non |
| GET | `/client/demands` | Demandes client | Oui |
| POST | `/client/demands` | Créer demande | Oui |
| GET | `/client/demands/{id}` | Détails demande | Oui |
| PUT | `/client/demands/{id}` | Modifier demande | Oui |
| GET | `/client/demands/create/{service}` | Données création | Oui |
| GET | `/client/demands/edit/{id}` | Données édition | Oui |
| GET | `/client/demands/verify-vehicle-situation/{vin}` | Vérifier situation | Oui |
| PUT | `/client/demands/attachments-update/{id}` | Mettre à jour pièces jointes | Oui |
| GET | `/client/get-pending-demands` | Demandes en attente | Oui |
| GET | `/client/get-vehicles` | Véhicules en ligne | Oui |
| GET | `/client/get-bought-vehicles` | Véhicules achetés | Oui |
| GET | `/client/cart` | Panier | Oui |
| POST | `/client/add-demand-to-cart` | Ajouter au panier | Oui |
| POST | `/client/validate-cart` | Valider panier | Oui |
| DELETE | `/client/cart-remove-demand/{id}` | Retirer du panier | Oui |
| DELETE | `/client/empty-cart` | Vider panier | Oui |
| POST | `/client/send-demand-otp` | Envoyer OTP demande | Oui |
| PUT | `/client/verify-demand-otp` | Vérifier OTP | Oui |
| GET | `/client/orders` | Commandes | Oui |
| GET | `/client/orders/{id}` | Détails commande | Oui |
| POST | `/client/submit-order` | Soumettre commande | Oui |
| POST | `/client/check-label` | Vérifier label | Oui |
| POST | `/client/check-number` | Vérifier numéro | Oui |
| POST | `/client/suggest-numbers` | Suggérer numéros | Oui |
| GET | `/client/certificates` | Certificats | Oui |
| GET | `/client/certificates/{id}` | Détails certificat | Oui |
| GET | `/client/sale-declarations/{ref}` | Déclaration vente | Oui |
| POST | `/client/add-characteristics` | Ajouter caractéristiques | Oui |
| DELETE | `/client/characteristic/{id}` | Supprimer caractéristique | Oui |

### 1.51 Véhicules Spéciaux

#### Véhicules Gouvernementaux
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/gov-vehicles` | Véhicules gouvernementaux | Oui |
| POST | `/gov-vehicles` | Créer véhicule | Oui |
| GET | `/gov-vehicles/{id}` | Détails véhicule | Oui |
| PUT | `/gov-vehicles/{id}` | Modifier véhicule | Oui |
| DELETE | `/gov-vehicles/{id}` | Supprimer véhicule | Oui |
| POST | `/import-gov-vehicles` | Importer véhicules | Oui |

#### Véhicules GMA (Gendarmerie)
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/gma-vehicles` | Véhicules GMA | Oui |
| POST | `/gma-vehicles` | Créer véhicule GMA | Oui |
| GET | `/gma-vehicles/{id}` | Détails véhicule | Oui |
| PUT | `/gma-vehicles/{id}` | Modifier véhicule | Oui |
| DELETE | `/gma-vehicles/{id}` | Supprimer véhicule | Oui |
| POST | `/import-gma-vehicles` | Importer véhicules | Oui |
| POST | `/validate-gma-vehicles` | Valider véhicules | Oui |
| POST | `/reject-gma-vehicles` | Rejeter véhicules | Oui |
| POST | `/get-gma-vehicle-stats` | Stats véhicules GMA | Oui |

#### Véhicules GMD (Douane)
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/gmd-vehicles` | Véhicules GMD | Oui |
| POST | `/gmd-vehicles` | Créer véhicule GMD | Oui |
| GET | `/gmd-vehicles/{id}` | Détails véhicule | Oui |
| PUT | `/gmd-vehicles/{id}` | Modifier véhicule | Oui |
| DELETE | `/gmd-vehicles/{id}` | Supprimer véhicule | Oui |
| POST | `/import-gmd-vehicles` | Importer véhicules | Oui |
| POST | `/validate-gmd-vehicles` | Valider véhicules | Oui |
| POST | `/reject-gmd-vehicles` | Rejeter véhicules | Oui |

#### Motos
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/motorcycles` | Liste motos | Oui |
| POST | `/motorcycles` | Créer moto | Oui |
| GET | `/motorcycles/{id}` | Détails moto | Oui |
| PUT | `/motorcycles/{id}` | Modifier moto | Oui |
| DELETE | `/motorcycles/{id}` | Supprimer moto | Oui |
| GET | `/motorcycle/file-format` | Format import | Oui |
| POST | `/motorcycle/import` | Importer motos | Oui |

### 1.52 Déclarations

#### Déclarations de Réforme
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/reform-declarations` | Déclarations réforme | Oui |
| POST | `/reform-declarations` | Créer déclaration | Oui |
| GET | `/reform-declarations/{id}` | Détails déclaration | Oui |
| PUT | `/reform-declarations/{id}` | Modifier déclaration | Oui |
| DELETE | `/reform-declarations/{id}` | Supprimer déclaration | Oui |
| GET | `/reform-declarations/{id}/generate-certificate` | Générer certificat | Oui |

#### Déclarations Vente aux Enchères
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/auction-sale-declarations` | Déclarations vente | Oui |
| POST | `/auction-sale-declarations` | Créer déclaration | Oui |
| GET | `/auction-sale-declarations/{id}` | Détails déclaration | Oui |
| PUT | `/auction-sale-declarations/{id}` | Modifier déclaration | Oui |
| DELETE | `/auction-sale-declarations/{id}` | Supprimer déclaration | Oui |
| GET | `/auction-sale-declarations/show-by-reference/{ref}` | Par référence | Oui |
| GET | `/auction-sale-declarations/{id}/generate-certificate` | Générer certificat | Oui |
| PUT | `/auction-sale-declarations/{id}/add-vehicle` | Ajouter véhicule | Oui |
| PUT | `/auction-sale-declarations/{id}/remove-vehicle` | Retirer véhicule | Oui |
| PUT | `/auction-sale-declarations/{id}/add-official` | Ajouter officiel | Oui |
| PUT | `/auction-sale-declarations/{id}/remove-official` | Retirer officiel | Oui |
| GET | `/auction-sale-declarations/stats` | Stats ventes | Oui |

#### Véhicules Vente aux Enchères
| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/auction-sale-vehicles/{id}` | Détails véhicule | Oui |
| PUT | `/auction-sale-vehicles/{id}` | Modifier véhicule | Oui |
| DELETE | `/auction-sale-vehicles/{id}` | Supprimer véhicule | Oui |

### 1.53 Documents & Logs

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/document-types` | Types documents | Oui |
| POST | `/document-types` | Créer type | Oui |
| GET/PUT/DELETE | `/document-types/{id}` | Gérer type | Oui |
| GET | `/required-document-types` | Types requis | Oui |
| POST | `/required-document-types` | Créer type requis | Oui |
| GET/PUT/DELETE | `/required-document-types/{id}` | Gérer type requis | Oui |
| GET | `/activity-logs` | Logs d'activité | Oui |
| GET | `/activity-logs/{id}` | Détails log | Oui |
| GET | `/meta-data` | Métadonnées | Non |
| PUT | `/meta-data` | Mettre à jour métadonnées | Non |
| GET | `/download/{id}` | Télécharger fichier | Non |

### 1.54 Tests & Recherche

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/test-notification` | Tester notifications | Oui |
| GET | `/anip-get-person` | Tester service ANIP | Oui |
| GET | `/registration/search/states` | États | Non |
| GET | `/registration/search/towns` | Villes | Non |
| GET | `/registration/search/districts` | Districts | Non |
| GET | `/registration/search/villages` | Villages | Non |

---

## 2. API Tierces (Services Externes)

### 2.1 APIs de Paiement

#### **FedaPay**
- **Usage** : Traitement des paiements pour les services d'immatriculation
- **Type** : API REST
- **Environnement** : Sandbox / Production
- **Endpoints Utilisés** :
  - `POST https://sandbox-api.fedapay.com/v1/transactions` - Créer transaction
  - `GET https://sandbox-api.fedapay.com/v1/transactions/{id}` - Vérifier statut
  - Webhooks pour notifications
- **Variables d'environnement** :
  ```
  FEDAPAY_PUBLIC_KEY=pk_sandbox_cb7jY3KvZ_FLNFp_LfqenqZA
  FEDAPAY_SECRET_KEY=sk_sandbox_WvbsARrZRcSxWd4ty8yn_3cj
  FEDAPAY_ENVIRONMENT=sandbox
  VITE_FEDAPAY_PUBLIC_KEY (frontend)
  VITE_FEDAPAY_API_URL=https://sandbox-api.fedapay.com
  ```
- **Fichiers** :
  - Backend: `app/Services/FedapayService.php`
  - Backend: `app/Http/Controllers/FedapayWebhookController.php`
  - Frontend Portal: `assets/js/fedapayCheckout.js`
  - Frontend Affiliate: `src/assets/js/fedapayCheckout.js`

#### **KKiaPay**
- **Usage** : Alternative de paiement mobile
- **Type** : API REST
- **Environnement** : Sandbox / Production
- **Variables d'environnement** :
  ```
  KKIAPAY_PUBLIC_KEY=5766c4e0824211efb2cd736c2a0bab43
  KKIAPAY_SECRET_KEY=tpk_5766ebf1824211efb2cd736c2a0bab43
  KKIAPAY_SANDBOX=true
  KKIAPAY_SECRET=tsk_5766ebf2824211efb2cd736c2a0bab43
  ```
- **Fichiers** :
  - Backend: `app/Services/KkiaPayTransactionService.php`

---

### 2.2 APIs de Notification

#### **Novu**
- **Usage** : Orchestration de notifications multi-canaux (Email, SMS, In-App, Push)
- **Type** : API REST
- **Base URL** : `https://api.novu.co/v1/`
- **Endpoints Utilisés** :
  - `POST /events/trigger` - Déclencher workflow
  - `POST /subscribers` - Gérer abonnés
  - `GET /notifications` - Liste notifications
- **Variables d'environnement** :
  ```
  NOVU_SECRET_KEY=6dbc7c10270af82ccde1212b96a5d1c8
  NOVU_PUBLIC_KEY=fBQFolGUuKeG
  NOVU_ENV_ID=66843a0f8d5e505deeca014d
  NOVU_API_KEY
  NOVU_BASE_API_URL=https://api.novu.co/v1/
  ```
- **Fichiers** :
  - Backend: `app/Notifications/NovuNotificationSender.php`
  - Custom Package: `ntech-libs/notifier-package/`

#### **Vonage (SMS)**
- **Usage** : Envoi de SMS
- **Type** : API REST
- **Fichiers** :
  - Backend: `app/Notifications/NovuNotificationSender.php` (toVonage method)

#### **Wirepick SMS**
- **Usage** : Service SMS alternatif
- **Type** : API REST
- **Base URL** : `https://apisms.wirepick.com/`
- **Variables d'environnement** :
  ```
  SMS_DRIVER=wirepick
  WIREPICK_HOST=https://apisms.wirepick.com/
  WIREPICK_USER
  WIREPICK_PASSWORD
  WIREPICK_SENDER_ID=MAEP
  ```
- **Fichiers** :
  - Backend: `app/Services/WirepickService.php`
  - Backend: `app/Services/SmsService.php`

---

### 2.3 APIs Gouvernementales (X-Road)

#### **X-Road Security Server**
- **Usage** : Passerelle sécurisée pour échanges de données gouvernementales
- **Type** : SOAP/REST Gateway
- **Base URL** : `https://common-ss.xroad.bj:8443`
- **Variables d'environnement** :
  ```
  XROAD_BASE_URL=https://common-ss.xroad.bj:8443
  ```

#### **ANIP (Agence Nationale d'Identification des Personnes)**
- **Usage** : Vérification d'identité par NPI (Numéro Personnel d'Identification)
- **Type** : SOAP (via X-Road)
- **Endpoints** :
  - Recherche personne par NPI
- **Fichiers** :
  - Backend: `app/Services/External/AnipService.php`
  - WSDL: `wdsl/anip-person.xml`, `wdsl/anip-person-test.xml`
- **Variables d'environnement** :
  ```
  ANIP_BASE_URL
  CHECK_NPI_URL=https://sandbox-api.simveb-bj.com/api/persons
  ```

#### **DGI (Direction Générale des Impôts)**
- **Usage** : Vérification fiscale par IFU (Identifiant Fiscal Unique)
- **Type** : REST (via X-Road)
- **Endpoints** :
  - `GET /entreprise/{ifu}` - Détails entreprise
- **Headers** :
  ```
  Uxp-Client: BJ/GOV/ANATT/SIMVEB
  Uxp-Service: BJ/GOV/DGI/CFISC/DETAIL-IFU/V1
  Authorization: Bearer {token}
  ```
- **Variables d'environnement** :
  ```
  DGI_BASE_URL
  DGI_TOKEN=eyJhbGciOiJIUzUxMiJ9... (JWT token)
  CHECK_IFU_URL=https://sandbox-api.simveb-bj.com/api/companies
  ```
- **Fichiers** :
  - Backend: `app/Services/External/DGIService.php`

#### **API Douane (Customs)**
- **Usage** : Informations véhicules douaniers
- **Type** : REST
- **Variables d'environnement** :
  ```
  DOUANE_API=https://sandbox-api.simveb-bj.com/api/
  DOUANE_BASE_URL
  SANDBOX_HOST=https://sandbox-api.simveb-bj.com/api/
  ```

---

### 2.4 Services Cloud & Stockage

#### **Amazon S3 (AWS)**
- **Usage** : Stockage de fichiers cloud (optionnel, par défaut local)
- **Type** : API REST
- **Variables d'environnement** :
  ```
  AWS_ACCESS_KEY_ID
  AWS_SECRET_ACCESS_KEY
  AWS_DEFAULT_REGION=us-east-1
  AWS_BUCKET
  AWS_USE_PATH_STYLE_ENDPOINT=false
  AWS_URL
  AWS_ENDPOINT
  FILESYSTEM_DISK=local (défaut)
  ```

---

### 2.5 Monitoring & Erreurs

#### **Sentry**
- **Usage** : Suivi d'erreurs et monitoring des performances
- **Type** : API REST
- **Endpoints** :
  - Envoi automatique d'erreurs
  - Tracking de transactions
- **Variables d'environnement** :
  ```
  SENTRY_LARAVEL_DSN=https://3c88e663578f3b27845c0cb554fdb51d@o4505782362177536.ingest.sentry.io/4505782364864512
  SENTRY_TRACES_SAMPLE_RATE=1.0
  SENTRY_RELEASE
  SENTRY_ENVIRONMENT
  ```
- **Packages** :
  - Backend: `sentry/sentry-laravel: ^3.8`
  - Frontend Portal: `@sentry/nuxt: ^8.24.0`
  - Frontend: `@sentry/vue: ^8.24.0`

---

### 2.6 Services Cartographiques

#### **Mapbox**
- **Usage** : Cartes interactives et géocodage
- **Type** : API REST / JavaScript SDK
- **Packages** :
  - Frontend Backoffice: `mapbox-gl: 2.15.0`
  - Frontend Backoffice: `@mapbox/mapbox-gl-geocoder: 5.0.1`

---

### 2.7 Services PDF

#### **Snappy (wkhtmltopdf)**
- **Usage** : Conversion HTML vers PDF
- **Type** : Binaire local
- **Packages** :
  - Backend: `barryvdh/laravel-snappy: ^1.0`
  - Backend: `h4cc/wkhtmltopdf-amd64: 0.12.x`
- **Fichiers** :
  - Backend: `app/Services/GeneratePdfService.php`

#### **PDFTron WebViewer**
- **Usage** : Visualisation PDF dans le navigateur
- **Type** : JavaScript SDK
- **Packages** :
  - Frontend Portal: `@pdftron/webviewer: ^10.11.1`
- **Fichiers** :
  - Frontend Portal: `components/PdfViewer.vue`

---

### 2.8 Services Email

#### **SMTP (Gmail)**
- **Usage** : Envoi d'emails
- **Type** : SMTP
- **Configuration** :
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=465
  MAIL_USERNAME=finplex.ntech@gmail.com
  MAIL_PASSWORD=lofeslljqmuvdqal
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@simveb.bj
  MAIL_FROM_NAME=SimVeb
  ```

---

### 2.9 Services de Données

#### **Redis**
- **Usage** : Cache et gestion de files d'attente
- **Type** : In-Memory Database
- **Variables d'environnement** :
  ```
  REDIS_HOST=127.0.0.1
  REDIS_PASSWORD=null
  REDIS_PORT=6379
  CACHE_DRIVER=file (défaut)
  QUEUE_CONNECTION=sync (défaut)
  ```

---

## 3. API Consommées par les Frontends

### 3.1 Portal (Nuxt.js)

**URL de Base** : `VITE_API_URL` (default: `http://localhost:8001/api`)

#### Authentification
- `POST /login` - Connexion utilisateur
- `PUT /change-space` - Changer de profil
- `GET /current-user` - Utilisateur actuel

#### Services
- `GET /client/services` - Liste des services
- `GET /client/services/{code}` - Service par code

#### Demandes
- `POST /client/demands` - Créer demande
- `GET /client/demands` - Liste demandes
- `GET /client/demands/{id}` - Détails demande
- `PUT /client/demands/{id}` - Modifier demande
- `GET /client/demands/create/{serviceId}` - Formulaire création
- `POST /client/add-demand-to-cart` - Ajouter au panier

#### Identité & Entreprise
- `GET /get-identity/{npi}` - Identité par NPI
- `GET /get-company/{ifu}` - Entreprise par IFU

#### Véhicules
- `GET /vehicles/{id}/plates` - Plaques du véhicule

#### Panier & Commandes
- `POST /client/submit-order` - Soumettre commande

---

### 3.2 Backoffice (Vue 3 + Vite)

**URL de Base** : `VITE_API_URL` (default: `http://localhost:8002/api`)

#### Authentification
- `POST /login` - Connexion admin
- `POST /logout` - Déconnexion
- `GET /current-user` - Utilisateur actuel
- `PUT /change-space` - Changer de profil
- `POST /login/send-otp` - Envoyer OTP
- `POST /login/resend-otp` - Renvoyer OTP

#### CRUD Générique
Le Backoffice utilise un store CRUD générique pour :
- `GET {url}` - Liste ressources
- `GET {url}/{id}` - Détails ressource
- `POST {url}` - Créer ressource
- `PUT {url}/{id}` - Modifier ressource
- `DELETE {url}/{id}` - Supprimer ressource
- `GET {url}/create` - Données formulaire création
- `GET {url}/{id}/edit` - Données formulaire édition

#### Gestion Demandes
- `GET /admin-demands` - Demandes admin
- `GET /demands/{id}` - Détails demande
- `POST /assign-demand-to-{entity}` - Affecter demande
- `POST /emit-print-order` - Émettre ordre impression
- `POST /close-demand` - Fermer demande
- `POST /validate-demand` - Valider demande
- `POST /reject-demand` - Rejeter demande
- `POST /verify-demand` - Vérifier demande
- `POST /suspend-demand` - Suspendre demande
- `GET /treatments/create` - Formulaire traitement
- `GET /generate-immatriculation-number` - Générer numéro

#### Impression
- `POST /print-orders/print-gray-card` - Imprimer carte grise

#### Services
- `GET /services` - Liste services
- `POST /services` - Créer service
- `GET /services/create` - Formulaire service
- `POST /toggle-service/{id}` - Activer/Désactiver

#### Affiliés
- `POST /affiliate-registration-requests/validate/{id}` - Valider affilié
- `POST /affiliate-registration-requests/reject/{id}` - Rejeter affilié
- `GET /affiliate/invitation/create` - Formulaire invitation
- `POST /affiliate/invite` - Envoyer invitation

---

### 3.3 Affiliate (Vue 3 + Vite)

**URL de Base** : `VITE_API_URL` (default: `http://localhost:8000/api`)

#### Authentification
- `POST /login` - Connexion
- `POST /logout` - Déconnexion
- `GET /current-user` - Utilisateur actuel
- `PUT /change-space` - Changer profil
- `POST /login/send-otp` - Envoyer OTP
- `POST /login/resend-otp` - Renvoyer OTP

#### CRUD Générique
Similar au Backoffice

#### Demandes Client
- `GET /get-service-id/{code}` - ID service par code
- `GET /client/demands` - Liste demandes
- `GET /client/demands/{id}` - Détails demande
- `POST /client/demands` - Créer demande
- `POST /client/add-demand-to-cart` - Ajouter au panier
- `PUT /client/demands/{id}` - Modifier demande
- `GET /client/demands/edit/{id}` - Formulaire édition
- `DELETE /client/demands/{id}` - Supprimer demande
- `GET /client/demands/create/{serviceId}` - Formulaire création
- `GET /client/demands/verify-vehicle-situation/{vin}` - Vérifier situation
- `POST /client/send-demand-otp` - Envoyer OTP demande
- `POST /client/verify-demand-otp` - Vérifier OTP

#### Identité & Entreprise
- `GET /get-company/{ifu}` - Entreprise par IFU
- `GET /get-identity/{npi}` - Identité par NPI

#### Véhicules
- `GET /get-vehicle` - Info véhicule
- `GET /client/get-vehicles` - Véhicules client
- `GET /gov-vehicles` - Véhicules gouvernementaux
- `POST /gov-vehicles` - Créer véhicule gouvernemental
- `POST /import-gov-vehicles` - Importer véhicules
- `GET /affiliate/vehicles/vehicle-details` - Détails véhicule

#### Motos
- `GET /motorcycles` - Liste motos
- `POST /motorcycles` - Créer moto
- `POST /motorcycle/import` - Importer motos
- `GET /motorcycle/file-format` - Format import

#### Panier & Commandes
- `GET /client/cart` - Articles panier
- `DELETE /client/cart-remove-demand/{id}` - Retirer du panier
- `POST /client/validate-cart` - Valider panier
- `POST /client/submit-order` - Soumettre commande
- `GET /client/orders/{id}` - Détails commande
- `POST /client/invoices/{id}/generate` - Générer facture

#### Déclarations Vente
- `GET /client/sale-declarations/{reference}` - Déclaration vente

#### Oppositions
- `GET /oppositions/create` - Formulaire opposition
- `GET /owner/vehicles` - Véhicules propriétaire
- `POST /oppositions` - Créer opposition
- `PUT /oppositions/{id}` - Modifier opposition

#### Gages
- `GET /pledge/create` - Formulaire gage
- `GET /pledge/vehicle/owner` - Véhicule & propriétaire
- `POST /pledge` - Créer gage
- `PUT /pledge/{id}` - Modifier gage
- `PUT /pledge-lift/{id}` - Levée de gage

#### Ordres Impression
- `GET /print-orders` - Liste ordres
- `POST /print-orders` - Créer ordre
- `GET /print-orders/search` - Rechercher ordres
- `GET /print-orders/create` - Formulaire création

#### Passages Véhicules
- `POST /vehicle-passages/get-vehicle-infos` - Info passage
- `GET /vehicle-passages` - Liste passages
- `POST /vehicle-passages` - Créer passage
- `GET /vehicle-passages/create` - Formulaire création

#### Autres
- `GET /blacklist-vehicles` - Véhicules liste noire
- `GET /notifications` - Notifications
- `GET /invitations` - Invitations
- `GET /profile-types/members` - Membres équipe
- `GET /alert-types` - Types alertes
- `GET /vehicle-alerts` - Alertes véhicules
- `GET /dashboard-stats` - Statistiques tableau de bord

---

## 4. Configuration des Clients HTTP

### 4.1 Portal (Nuxt.js)

**Fichier** : `helpers/useApi.ts`

```typescript
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiUrl || 'http://localhost:8000/api'

// Axios instance avec:
- Base URL: apiBaseUrl
- Authorization: Bearer token (from cookie)
- Intercepteurs: gestion 401/403
```

**Variables d'environnement** :
```env
VITE_API_URL=http://localhost:8001/api
VITE_FEDAPAY_PUBLIC_KEY=""
VITE_CLIENT_ID=""
VITE_CLIENT_SECRET=""
VITE_PORTAL_URL=http://localhost:8003
VITE_COOKIE_DOMAIN=localhost
```

---

### 4.2 Backoffice (Vue 3)

**Fichiers** :
- `src/composable/axiosClient.ts` - Client principal
- `src/composable/useAuthApi.ts` - Client auth

```typescript
// Configuration:
- Base URL: import.meta.env.VITE_API_URL
- Authorization: Bearer token (from userSession store)
- Intercepteurs: redirect 401 to /login
- Notifications: Notyf
```

**Variables d'environnement** :
```env
VITE_API_URL=http://localhost:8002/api
VITE_CLIENT_ID=9d1ffaab-7aa4-43fe-9a90-2726fc316351
VITE_CLIENT_SECRET=cwWM3KgXdMb91WnHrOzVGT1xNb3sQfRbRPjTKBHE
VITE_COOKIE_DOMAIN=localhost
```

---

### 4.3 Affiliate (Vue 3)

**Fichiers** :
- `src/assets/js/axios/client.js` - Client principal
- `src/assets/js/axios/auth-client.js` - Client auth

```javascript
// Configuration:
- API URL: import.meta.env.VITE_API_URL
- Login URL: import.meta.env.VITE_LOGIN_URL
- Authorization: Bearer token (from cookies)
- Intercepteurs: clear storage on 401
- Alerts: Custom Alert component
```

**Variables d'environnement** :
```env
VITE_API_URL=http://localhost:8000/api
VITE_LOGIN_URL=http://localhost:8000/login
VITE_CLIENT_ID=9a03f3d1-8bcf-4d63-b649-980e327c0c86
VITE_CLIENT_SECRET=JAPjLnGdS6gQK1WVUJ7SRLRbg0YKV9ZHbZVgh5s6
VITE_FEDAPAY_PRIVATE_KEY=sk_sandbox_tclJppAyE-2EffzRl5VlZfgq
VITE_FEDAPAY_PUBLIC_KEY=pk_sandbox_A86QDBH9O8mmnwf3qF0___ef
VITE_FEDAPAY_API_URL=https://sandbox-api.fedapay.com
VITE_COOKIE_DOMAIN=localhost
```

---

## 📊 Résumé

### Statistiques API

| Catégorie | Nombre d'Endpoints |
|-----------|-------------------|
| **API Backend (Laravel)** | 400+ |
| **API Tierces Externes** | 15+ services |
| **Frontends (Total)** | 3 applications |

### Technologies API

| Technologie | Usage |
|-------------|-------|
| **REST** | API principale backend |
| **SOAP** | ANIP (via X-Road) |
| **OAuth2** | Authentification (Laravel Passport) |
| **JWT** | Tokens DGI |
| **Webhooks** | FedaPay, Novu |
| **WebSocket** | Pusher (configuré, pas utilisé) |

### Protocoles de Sécurité

- **HTTPS** : Toutes les communications externes
- **Bearer Tokens** : Authentification API
- **OAuth2** : Authentification client
- **X-Road** : Sécurisation échanges gouvernementaux
- **CORS** : Configuration côté backend
- **Cookie Domain** : Gestion multi-domaine

---

## 🔐 Sécurité & Bonnes Pratiques

### Recommandations

1. **Secrets** : Ne jamais exposer les clés API dans le code
2. **Variables d'environnement** : Utiliser `.env` pour configuration
3. **Tokens** : Stockage sécurisé dans cookies HttpOnly
4. **HTTPS** : Obligatoire en production
5. **Rate Limiting** : Implémenter pour toutes les API publiques
6. **Validation** : Toujours valider côté backend
7. **Logging** : Tracer toutes les requêtes API (Laravel Telescope)
8. **Monitoring** : Sentry pour suivi des erreurs

---

**Documentation générée le** : 2025-01-19
**Version du projet** : SIMVEB v1.0
**Maintenu par** : Équipe SIMVEB
