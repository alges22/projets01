# Modules de Connexion et Inscription SIMVEB

**Système d'Authentification et Inscription**

Version: 1.0
Date: 2025-12-08

---

## Table des Matières

1. [Architecture Globale](#1-architecture-globale)
2. [Backend API](#2-backend-api)
3. [Portal Public](#3-portal-public)
4. [Backoffice Admin](#4-backoffice-admin)
5. [Affiliate Institutions](#5-affiliate-institutions)
6. [Flux Complets](#6-flux-complets)
7. [Sécurité](#7-sécurité)
8. [Types d'Utilisateurs](#8-types-dutilisateurs)

---

## 1. Architecture Globale

### 1.1 Vue d'ensemble

SIMVEB utilise une architecture OAuth2 centralisée avec Laravel Passport pour l'authentification :

```
┌─────────────────────────────────────────────────────────────┐
│                    Backend API (Laravel)                     │
│                  OAuth2 + Laravel Passport                   │
│                     OTP Authentication                       │
└────────┬──────────────────────┬──────────────────┬──────────┘
         │                      │                  │
         ▼                      ▼                  ▼
┌────────────────┐    ┌────────────────┐  ┌────────────────┐
│  Portal        │    │  Backoffice    │  │  Affiliate     │
│  (Nuxt 3)      │    │  (Vue 3)       │  │  (Vue 3)       │
│  Citoyens      │    │  Admin         │  │  Institutions  │
└────────────────┘    └────────────────┘  └────────────────┘
```

### 1.2 Principes Clés

- **🔐 OAuth2** : Authentification standardisée avec Passport
- **📱 OTP obligatoire** : Code à usage unique pour chaque connexion
- **🔄 Multi-profils** : Un utilisateur peut avoir plusieurs profils
- **✅ Validation externe** : Intégration ANIP pour vérification d'identité
- **🏢 Workflow d'approbation** : Les entreprises nécessitent validation admin

---

## 2. Backend API

### 2.1 Endpoints d'Authentification

#### 🔑 Connexion

```
POST /login/send-otp         # Envoi du code OTP
POST /login/resend-otp       # Renvoi du code OTP
POST /login                  # Authentification OAuth2
POST /logout                 # Déconnexion
GET  /current-user           # Utilisateur connecté
PUT  /change-space           # Changement de profil
```

#### 📝 Inscription

```
POST /register/init                # Initialisation inscription
POST /register/resend-otp          # Renvoi OTP inscription
POST /register/check-otp           # Vérification OTP
POST /register/store               # Finalisation inscription
GET  /register/space-documents     # Documents requis entreprise
```

#### 🔐 Mot de passe

```
POST /forgot-password              # Demande réinitialisation
GET  /forgot-password/{token}      # Vérification token
POST /reset-password               # Réinitialisation
PUT  /reset-password-expired       # Reset si expiré
PUT  /update-password              # Mise à jour
```

### 2.2 Flow de Connexion

#### Étape 1 : Demande d'OTP

**Endpoint** : `POST /login/send-otp`

**Body** :
```json
{
  "npi": "1234567890"
}
```

**Validation** :
- `npi` : required, string, size:10, exists:users,username

**Processus** :
1. Récupération des données ANIP
2. Génération code OTP 4 chiffres
3. Hash bcrypt du code
4. Stockage en cache (clé: `{IP}-one-time-password`)
5. Envoi par SMS
6. Envoi par email

**Réponse** :
```json
{
  "npi": "1234567890",
  "telephone": "***7890",
  "otp_duration": 5,
  "message": "Code OTP envoyé sur votre numéro de téléphone"
}
```

#### Étape 2 : Authentification OAuth2

**Endpoint** : `POST /login`

**Body** :
```json
{
  "username": "1234567890",
  "password": "1234",
  "grant_type": "password",
  "client_id": "<CLIENT_ID>",
  "client_secret": "<CLIENT_SECRET>"
}
```

**Validation** :
- Vérification OTP via cache
- Comparaison avec Hash::check()

**Processus** :
1. Vérifier code OTP en cache
2. Mise à jour temporaire du mot de passe
3. Génération token OAuth2 Passport
4. Définition du profil en ligne (online_profile_id)
5. Suppression mot de passe temporaire
6. Suppression cache OTP

**Réponse** :
```json
{
  "token_type": "Bearer",
  "expires_in": 31536000,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "refresh_token": "def502..."
}
```

### 2.3 Flow d'Inscription Personne Physique

#### Étape 1 : Initialisation

**Endpoint** : `POST /register/init`

**Body** :
```json
{
  "person_type": "physical",
  "npi": "1234567890",
  "email": "user@example.com"
}
```

**Validation** :
```php
'person_type' => 'required|in:physical,moral'
'npi' => 'required|string|size:10|unique:identities,npi|unique:users,username'
'email' => 'required|email|unique:users,email'
```

**Processus** :
1. Vérifier que NPI n'existe pas
2. Récupérer données ANIP (IdentityService)
3. Stocker données en cache (30 minutes)
4. Générer et envoyer OTP par SMS

**Réponse** :
```json
{
  "npi": "1234567890",
  "telephone": "***7890",
  "otp_duration": 5
}
```

#### Étape 2 : Vérification OTP

**Endpoint** : `POST /register/check-otp`

**Body** :
```json
{
  "person_type": "physical",
  "npi": "1234567890",
  "otp": "1234"
}
```

**Processus** :
1. Vérifier code OTP
2. Récupérer données en cache

**Réponse** :
```json
{
  "user_data": {
    "npi": "1234567890",
    "lastname": "DOE",
    "firstname": "John",
    "birthdate": "1990-01-01",
    "telephone": "+22912345678",
    "email": "user@example.com"
  }
}
```

#### Étape 3 : Finalisation

**Endpoint** : `POST /register/store`

**Body** :
```json
{
  "person_type": "physical",
  "npi": "1234567890",
  "state_id": 1,
  "town_id": 10,
  "district_id": 50,
  "village_id": 200,
  "house": "Maison blanche"
}
```

**Validation** :
```php
'person_type' => 'required|in:physical,moral'
'npi' => 'required|size:10'
'state_id' => 'required|exists:states,id'
'town_id' => 'required|exists:towns,id'
'district_id' => 'required|exists:districts,id'
'village_id' => 'required|exists:villages,id'
'house' => 'required|string'
```

**Processus (Transaction DB)** :
1. Créer Identity
2. Créer User
3. Créer Profile (type: "user")
4. Envoyer notification de succès
5. Supprimer cache

**Réponse** :
```json
{
  "success": true,
  "message": "Inscription réussie"
}
```

### 2.4 Flow d'Inscription Personne Morale (Entreprise)

#### Étape 1 : Initialisation

**Endpoint** : `POST /register/init`

**Body** :
```json
{
  "person_type": "moral",
  "ifu": "1234567890123"
}
```

**Validation** :
```php
'person_type' => 'required|in:physical,moral'
'ifu' => 'required|string|size:13|unique:institutions,ifu'
```

**Processus** :
1. Vérifier que IFU n'existe pas
2. Récupérer données entreprise (IdentityService->getIdentityByIfu)
3. Stocker en cache (30 minutes)
4. Envoyer OTP par **EMAIL** (pas SMS)

#### Étape 2 : Vérification OTP

**Endpoint** : `POST /register/check-otp`

**Body** :
```json
{
  "person_type": "moral",
  "ifu": "1234567890123",
  "otp": "1234"
}
```

**Réponse** :
```json
{
  "company_data": {
    "ifu": "1234567890123",
    "name": "Entreprise SARL",
    "email": "contact@entreprise.bj",
    "telephone": "+22912345678",
    "address": "Cotonou, Bénin"
  }
}
```

#### Étape 3 : Finalisation

**Endpoint** : `POST /register/store`

**Body** :
```json
{
  "person_type": "moral",
  "ifu": "1234567890123",
  "first_member_npi": "0987654321",
  "documents": [
    {
      "type_id": 1,
      "file": "<UPLOAD_FILE>"
    },
    {
      "type_id": 2,
      "file": "<UPLOAD_FILE>"
    }
  ]
}
```

**Validation** :
```php
'person_type' => 'required|in:physical,moral'
'ifu' => 'required|size:13'
'first_member_npi' => 'required|size:10'
'documents' => 'nullable|array'
'documents.*.type_id' => 'required|exists:document_types,id'
'documents.*.file' => 'required|file'
```

**Processus (Transaction DB)** :
1. Vérifier NPI du premier administrateur (ANIP)
2. Créer Institution (IdentityService)
3. Créer SpaceRegistrationRequest (statut: EN ATTENTE)
4. Upload des documents
5. Envoyer notification à l'entreprise

**Réponse** :
```json
{
  "success": true,
  "message": "Demande d'inscription enregistrée. En attente de validation par un administrateur."
}
```

**⚠️ Note** : L'entreprise ne peut pas se connecter tant que la demande n'est pas validée par un admin dans le backoffice.

### 2.5 Modèles de Données

#### User

**Fichier** : `app/Models/Account/User.php`

```php
{
  "id": "uuid",
  "username": "1234567890",        // NPI (unique)
  "email": "user@example.com",     // unique
  "password": "hashed",            // Temporaire pour OTP
  "identity_id": "uuid",           // FK Identity
  "online_profile_id": "uuid",     // FK Profile actif
  "email_verified_at": "datetime",
  "disabled_at": "datetime"        // Suspension compte
}
```

**Relations** :
- `identity()` : BelongsTo Identity
- `profiles()` : HasMany Profile
- `onlineProfile()` : BelongsTo Profile
- `declarant()` : HasOne Declarant

#### Profile

**Fichier** : `app/Models/Auth/Profile.php`

```php
{
  "id": "uuid",
  "user_id": "uuid",               // FK User
  "type_id": "uuid",               // FK ProfileType
  "space_id": "uuid",              // FK Space (entreprises)
  "institution_id": "uuid",        // FK Institution
  "identity_id": "uuid",           // FK Identity
  "number": "string",              // Numéro de profil
  "suspended_at": "datetime"       // Suspension profil
}
```

**Relations** :
- `user()` : BelongsTo User
- `type()` : BelongsTo ProfileType
- `space()` : BelongsTo Space
- `identity()` : BelongsTo Identity
- `demands()` : HasMany Demand

#### ProfileType

**Fichier** : `app/Models/Auth/ProfileType.php`

**Types disponibles** :

| Code | Libellé | Description |
|------|---------|-------------|
| `user` | Usager/Vendeur | Citoyen standard |
| `auctioneer` | Commissaire priseur | Ventes aux enchères |
| `company` | Entreprise | Société |
| `distributor` | Concessionnaire | Distributeur véhicules |
| `bank` | Banque | Institution bancaire |
| `approved` | Agréé | Professionnel agréé |
| `affiliate` | Affilié | Institution affiliée |
| `interpol` | Interpol | Police internationale |
| `anatt` | ANATT | Agence transport |
| `police` | Police | Forces de l'ordre |
| `central_garage` | Garage central | Garage gouvernemental |
| `gma` | GM Affaires | Gestionnaire ministériel |
| `gmd` | GM Diplomatie | Gestionnaire diplomatique |
| `court` | Tribunal | Instances judiciaires |

### 2.6 Système OTP

**Fichier** : `ntech-libs/users-package/src/Services/Auth/OtpService.php`

**Configuration** :
- **Code** : 4 chiffres
- **Environnement dev** : code fixe "1234"
- **Production** : génération aléatoire
- **Stockage** : Cache Redis (clé: `{IP}-one-time-password`)
- **Durée** : 5 minutes (configurable via MetaData)
- **Hash** : bcrypt

**Canaux d'envoi** :
- **SMS** : via SmsService (Vonage)
- **Email** : via système de notification

**Cas spéciaux** :
- NPI dans liste `Utils::LOCAL_NPI` : toujours "1234"

### 2.7 Middlewares

#### Authenticate

**Fichier** : `app/Http/Middleware/Authenticate.php`

```php
protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        return route('login');
    }
}
```

**Guard** : `api` (Passport OAuth2)

#### SpaceAccessMiddleware

**Fichier** : `app/Http/Middleware/SpaceAcessMiddleware.php`

**Vérifications** :
```php
// 1. Space non suspendu
if ($space->status !== 'active') {
    return response()->json(['message' => 'Espace suspendu'], 403);
}

// 2. Profile non suspendu
if ($profile->suspended_at !== null) {
    return response()->json(['message' => 'Profil suspendu'], 403);
}
```

**Routes protégées** : Toutes les routes avec middleware `['auth:api', 'space.access']`

### 2.8 Configuration OAuth2

**Fichier** : `config/auth.php`

```php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \App\Models\Account\User::class,
    ],
]
```

**Fichier** : `config/passport.php`

- Client UUIDs : activé
- Personal access tokens
- Refresh tokens
- Durée : configurable

**Installation** :
```bash
php artisan passport:install
php artisan passport:client --password
```

### 2.9 Système d'Invitations

**Fichier** : `app/Models/Auth/Invitation.php`

**Flow** :
1. Un espace envoie une invitation à un NPI
2. Invitation contient : NPI, space_id, profile_type_id
3. Destinataire reçoit notification
4. Il peut accepter ou refuser
5. Si accepté : création automatique du Profile

**Endpoints** :
```
POST /invitations                  # Créer invitation
PUT  /invitations/{id}/validate    # Accepter
PUT  /invitations/{id}/deny        # Refuser
POST /invitations/{id}/resend      # Renvoyer
```

---

## 3. Portal Public

### 3.1 Pages d'Authentification

#### 📂 Structure

```
simveb-portal-design-develop/
├── pages/
│   └── auth/
│       ├── login/
│       │   ├── index.vue              # Saisie NPI
│       │   └── otp.vue                # Saisie OTP
│       └── register/
│           ├── personne.vue           # Inscription physique
│           └── entreprise.vue         # Inscription morale
├── components/
│   ├── register_steps/
│   │   ├── Register.vue               # Étape 1 : Saisie NPI/Email
│   │   ├── ProcessVerification.vue    # Étape 2 : Vérification OTP
│   │   └── InformationsConfirm.vue    # Étape 3 : Confirmation
│   └── register_entreprise_steps/
│       ├── Register.vue               # Étape 1 : Saisie IFU
│       ├── ProcessVerification.vue    # Étape 2 : Vérification OTP
│       └── InformationsConfirm.vue    # Étape 3 : Documents
└── stores/
    ├── auth.ts                        # Store authentification
    ├── login.js                       # Store login
    └── register.js                    # Store inscription
```

### 3.2 Flow de Connexion

#### Page 1 : Saisie NPI

**Fichier** : `pages/auth/login/index.vue`

**Template** :
```vue
<template>
  <form @submit.prevent="sendOTP">
    <input
      v-model="npi"
      type="text"
      pattern="[0-9]{10}"
      maxlength="10"
      placeholder="Numéro NPI"
      required
    />
    <button type="submit">Continuer</button>
  </form>
</template>
```

**Script** :
```javascript
const sendOTP = async () => {
  try {
    await $fetch('/login/send-otp', {
      method: 'POST',
      body: { npi: npi.value }
    });

    loginStore.npi = npi.value;
    navigateTo('/auth/login/otp');
  } catch (error) {
    // Gestion erreur
  }
}
```

#### Page 2 : Vérification OTP

**Fichier** : `pages/auth/login/otp.vue`

**Template** :
```vue
<template>
  <form @submit.prevent="login">
    <InputCode
      v-model="otp"
      :length="4"
      :autofocus="true"
    />
    <button type="submit">Se connecter</button>
    <button @click="resendOTP">Renvoyer le code</button>
  </form>
</template>
```

**Script** :
```javascript
const login = async () => {
  const authStore = useAuthStore();

  const data = {
    username: loginStore.npi,
    password: otp.value,
    grant_type: 'password',
    client_id: runtimeConfig.public.clientId,
    client_secret: runtimeConfig.public.clientSecret
  };

  await authStore.authenticateUser(data);
  navigateTo('/');
}
```

### 3.3 Flow d'Inscription Personne Physique

#### Étape 1 : Saisie Email + NPI

**Fichier** : `components/register_steps/Register.vue`

**Champs** :
- Email (email, required, unique)
- NPI (string, size:10, required, unique)

**Action** :
```javascript
const initRegister = async () => {
  const response = await $fetch('/register/init', {
    method: 'POST',
    body: {
      person_type: 'physical',
      npi: form.npi,
      email: form.email
    }
  });

  registerStore.nextStep();
}
```

#### Étape 2 : Vérification OTP

**Fichier** : `components/register_steps/ProcessVerification.vue`

**Template** :
```vue
<InputCode
  v-model="otp"
  :length="4"
  @complete="verifyOTP"
/>
```

**Action** :
```javascript
const verifyOTP = async () => {
  const response = await $fetch('/register/check-otp', {
    method: 'POST',
    body: {
      person_type: 'physical',
      npi: registerStore.npi,
      otp: otp.value
    }
  });

  registerStore.setUserData(response.user_data);
  registerStore.nextStep();
}
```

#### Étape 3 : Confirmation Adresse

**Fichier** : `components/register_steps/InformationsConfirm.vue`

**Champs** :
```vue
<select v-model="form.state_id">
  <!-- Départements -->
</select>

<select v-model="form.town_id">
  <!-- Communes -->
</select>

<select v-model="form.district_id">
  <!-- Arrondissements -->
</select>

<select v-model="form.village_id">
  <!-- Villages/Quartiers -->
</select>

<input v-model="form.house" placeholder="Maison" />
```

**Recherche géographique** :
```javascript
// Chargement des départements
const states = await $fetch('/registration/search/states');

// Chargement des communes
const towns = await $fetch(`/registration/search/towns?state_id=${state_id}`);

// Chargement des arrondissements
const districts = await $fetch(`/registration/search/districts?town_id=${town_id}`);

// Chargement des villages
const villages = await $fetch(`/registration/search/villages?district_id=${district_id}`);
```

**Finalisation** :
```javascript
const finalize = async () => {
  await $fetch('/register/store', {
    method: 'POST',
    body: {
      person_type: 'physical',
      npi: registerStore.npi,
      state_id: form.state_id,
      town_id: form.town_id,
      district_id: form.district_id,
      village_id: form.village_id,
      house: form.house
    }
  });

  navigateTo('/auth/login');
}
```

### 3.4 Flow d'Inscription Entreprise

#### Étape 1 : Saisie IFU

**Fichier** : `components/register_entreprise_steps/Register.vue`

**Champs** :
- IFU (string, size:13, required, unique)

#### Étape 2 : Vérification OTP (Email)

**Fichier** : `components/register_entreprise_steps/ProcessVerification.vue`

**⚠️ Note** : OTP envoyé par EMAIL (pas SMS)

#### Étape 3 : Documents + NPI Admin

**Fichier** : `components/register_entreprise_steps/InformationsConfirm.vue`

**Champs** :
```vue
<input
  v-model="form.first_member_npi"
  type="text"
  pattern="[0-9]{10}"
  placeholder="NPI du premier administrateur"
/>

<div v-for="doc in requiredDocuments" :key="doc.id">
  <label>{{ doc.name }}</label>
  <input
    type="file"
    @change="handleFileUpload(doc.id, $event)"
    accept=".pdf,.jpg,.jpeg,.png"
  />
</div>
```

**Chargement documents requis** :
```javascript
const requiredDocuments = await $fetch('/register/space-documents');
```

**Finalisation** :
```javascript
const finalize = async () => {
  const formData = new FormData();
  formData.append('person_type', 'moral');
  formData.append('ifu', registerStore.ifu);
  formData.append('first_member_npi', form.first_member_npi);

  form.documents.forEach((doc, index) => {
    formData.append(`documents[${index}][type_id]`, doc.type_id);
    formData.append(`documents[${index}][file]`, doc.file);
  });

  await $fetch('/register/store', {
    method: 'POST',
    body: formData
  });

  // Message : "En attente de validation"
  navigateTo('/auth/login');
}
```

### 3.5 Stores Pinia

#### Auth Store

**Fichier** : `stores/auth.ts`

```typescript
export const useAuthStore = defineStore('auth', {
  state: () => ({
    authenticated: false,
    loading: false
  }),

  actions: {
    async authenticateUser(data: any) {
      const response = await $fetch('/login', {
        method: 'POST',
        body: data
      });

      // Stockage token dans cookie
      const cookie = useCookie('token', {
        domain: runtimeConfig.public.cookieDomain,
        maxAge: 60 * 60 * 24 * 365 // 1 an
      });

      cookie.value = response.access_token;
      this.authenticated = true;

      return response;
    },

    async switchProfile(profile_id: string) {
      await $fetch('/change-space', {
        method: 'PUT',
        body: { profile_id }
      });
    },

    logUserOut() {
      const cookie = useCookie('token');
      cookie.value = null;
      this.authenticated = false;
      navigateTo('/');
    }
  }
});
```

#### Register Store

**Fichier** : `stores/register.js`

```javascript
export const useRegisterStore = defineStore('register', {
  state: () => ({
    activeStep: 0,           // 0, 1, 2
    person_type: null,       // 'physical' ou 'moral'
    npi: null,
    ifu: null,
    email: null,
    user_data: null,         // Données après OTP
    company_data: null
  }),

  actions: {
    nextStep() {
      this.activeStep++;
    },

    previousStep() {
      this.activeStep--;
    },

    setUserData(data) {
      this.user_data = data;
    },

    setCompanyData(data) {
      this.company_data = data;
    },

    reset() {
      this.activeStep = 0;
      this.npi = null;
      this.ifu = null;
      this.email = null;
      this.user_data = null;
      this.company_data = null;
    }
  }
});
```

### 3.6 Middleware de Protection

**Fichier** : `middleware/auth.global.js`

```javascript
export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore();
  const token = useCookie('token');

  // Vérifier présence du token
  if (token.value) {
    authStore.authenticated = true;
  }

  // Routes publiques
  const publicRoutes = [
    'auth-login',
    'auth-login-otp',
    'auth-register-personne',
    'auth-register-entreprise',
    'code'
  ];

  // Si connecté et sur page login → redirect /
  if (authStore.authenticated && to.name === 'auth-login') {
    return navigateTo('/');
  }

  // Si non connecté et route protégée → redirect login
  if (!authStore.authenticated && !publicRoutes.includes(to.name)) {
    return navigateTo('/auth/login');
  }
});
```

### 3.7 Configuration API

**Fichier** : `.env`

```bash
VITE_API_URL=https://api.simveb-bj.com
VITE_CLIENT_ID=<CLIENT_ID>
VITE_CLIENT_SECRET=<CLIENT_SECRET>
VITE_COOKIE_DOMAIN=.simveb-bj.com
```

**Fichier** : `nuxt.config.ts`

```typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiUrl: process.env.VITE_API_URL,
      clientId: process.env.VITE_CLIENT_ID,
      clientSecret: process.env.VITE_CLIENT_SECRET,
      cookieDomain: process.env.VITE_COOKIE_DOMAIN
    }
  }
});
```

---

## 4. Backoffice Admin

### 4.1 Page de Connexion

**Fichier** : `src/pages/auth/login.vue`

**Template** :
```vue
<template>
  <form @submit.prevent="handleSendOTP">
    <input
      v-model="npi"
      type="text"
      pattern="[0-9]{10}"
      maxlength="10"
      placeholder="NPI"
      required
    />
    <button type="submit">Se connecter</button>
  </form>

  <!-- Modal OTP -->
  <VModal v-model="showOTPModal">
    <VInputCode
      v-model="otp"
      :length="4"
      @complete="handleLogin"
    />
    <button @click="handleLogin">Valider</button>
  </VModal>
</template>
```

**Script** :
```typescript
const handleSendOTP = async () => {
  const userSession = useUserSessionStore();

  try {
    const response = await userSession.sendOTP(npi.value);

    telephone.value = response.telephone;
    otpDuration.value = response.otp_duration;
    showOTPModal.value = true;
  } catch (error) {
    // Gestion erreur
  }
}

const handleLogin = async () => {
  const userSession = useUserSessionStore();

  const data = {
    username: npi.value,
    password: otp.value,
    grant_type: 'password',
    client_id: import.meta.env.VITE_CLIENT_ID,
    client_secret: import.meta.env.VITE_CLIENT_SECRET
  };

  await userSession.login(data);
  router.push('/');
}
```

### 4.2 User Session Store

**Fichier** : `src/stores/userSession.ts`

```typescript
export const useUserSessionStore = defineStore('userSession', {
  state: () => ({
    token: Cookies.get('token'),
    user: null,
    identity: null,
    staff: null,
    roles: [],
    profiles: [],
    online_profile: null,
    permissions: [],
    loading: false
  }),

  getters: {
    isLoggedIn: (state) => state.token !== undefined
  },

  actions: {
    async sendOTP(npi: string, resend = false) {
      const endpoint = resend ? '/login/resend-otp' : '/login/send-otp';

      const response = await api.post(endpoint, { npi });
      return response.data;
    },

    async login(data: any) {
      this.loading = true;

      try {
        const response = await authClient.post('/login', data);

        // Stockage token dans cookie
        Cookies.set('token', response.data.access_token, {
          domain: import.meta.env.VITE_COOKIE_DOMAIN,
          expires: 365
        });

        this.token = response.data.access_token;

        // Récupération utilisateur
        await this.fetchUser();
      } finally {
        this.loading = false;
      }
    },

    async fetchUser() {
      const response = await api.get('/current-user');

      this.user = response.data.user;
      this.identity = response.data.identity;
      this.staff = response.data.staff;
      this.profiles = response.data.profiles;
      this.online_profile = response.data.online_profile;
      this.roles = response.data.roles;
      this.permissions = response.data.permissions;

      // Stockage du code profil
      if (this.online_profile) {
        Cookies.set('code', this.online_profile.type.code);
      }
    },

    async switchProfile(profile_id: string, code: string) {
      await api.put('/change-space', { profile_id });

      Cookies.set('code', code);
      await this.fetchUser();
    },

    async logout() {
      await api.post('/logout');

      Cookies.remove('token');
      Cookies.remove('code');
      localStorage.clear();

      this.$reset();
      window.location.href = '/auth/login';
    }
  }
});
```

### 4.3 API Client Configuration

**Fichier** : `src/utils/api/client.ts`

```typescript
import axios from 'axios';
import Cookies from 'js-cookie';

// Client API standard
export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Intercepteur pour ajouter le token
api.interceptors.request.use((config) => {
  const token = Cookies.get('token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

// Gestion des erreurs 401
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      Cookies.remove('token');
      window.location.href = '/auth/login';
    }

    return Promise.reject(error);
  }
);

// Client pour l'authentification OAuth2
export const authClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});
```

### 4.4 Middleware de Protection

**Fichier** : `src/router/index.ts`

```typescript
router.beforeEach(async (to, from, next) => {
  const userSession = useUserSessionStore();

  // Routes publiques
  if (to.path === '/auth/login') {
    if (userSession.isLoggedIn) {
      return next('/');
    }
    return next();
  }

  // Routes protégées
  if (!userSession.isLoggedIn) {
    return next('/auth/login');
  }

  // Charger l'utilisateur si pas encore fait
  if (!userSession.user) {
    await userSession.fetchUser();
  }

  next();
});
```

### 4.5 Gestion des Rôles et Permissions

**Utilisation dans les composants** :

```vue
<template>
  <!-- Affichage conditionnel selon rôle -->
  <div v-if="hasRole('admin')">
    Contenu admin
  </div>

  <!-- Affichage conditionnel selon permission -->
  <button v-if="hasPermission('vehicles.create')">
    Créer véhicule
  </button>
</template>

<script setup>
import { computed } from 'vue';
import { useUserSessionStore } from '@/stores/userSession';

const userSession = useUserSessionStore();

const hasRole = (role) => {
  return userSession.roles.includes(role);
};

const hasPermission = (permission) => {
  return userSession.permissions.includes(permission);
};
</script>
```

**Système de permissions** :

Le backoffice utilise **Spatie Laravel-permission** côté backend :
- Rôles : admin, agent, manager, etc.
- Permissions : vehicles.view, vehicles.create, orders.approve, etc.

---

## 5. Affiliate Institutions

### 5.1 Système Multi-Profils

L'application Affiliate gère plusieurs types d'institutions avec des domaines séparés :

**Fichier** : `space-config.js`

```javascript
export default {
  police: 'https://police.simveb-bj.com',
  interpol: 'https://interpol.simveb-bj.com',
  bank: 'https://bank.simveb-bj.com',
  central_garage: 'https://garage.simveb-bj.com',
  approved: 'https://approved.simveb-bj.com',
  affiliate: 'https://affiliate.simveb-bj.com',
  auctioneer: 'https://auctioneer.simveb-bj.com',
  gma: 'https://gma.simveb-bj.com',
  gmd: 'https://gmd.simveb-bj.com',
  court: 'https://court.simveb-bj.com'
};
```

### 5.2 Page de Connexion

**Fichier** : `src/views/Auth/LoginView.vue`

**Flow identique aux autres applications** :
1. Saisie NPI
2. Envoi OTP
3. Vérification OTP
4. Authentification OAuth2

### 5.3 Auth Store

**Fichier** : `src/stores/auth.js`

```javascript
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    roles: [],
    permissions: [],
    isLoggedIn: false,
    loading: false,
    online_profile: null,
    profiles: [],
    theme: null          // Thème selon le space->template
  }),

  actions: {
    async sendOTP(npi, resend = false) {
      const endpoint = resend ? '/login/resend-otp' : '/login/send-otp';
      return await api.post(endpoint, { npi });
    },

    async login(data) {
      const response = await authClient.post('/login', data);

      Cookies.set('token', response.data.access_token, {
        domain: import.meta.env.VITE_COOKIE_DOMAIN,
        expires: 365
      });

      this.isLoggedIn = true;
      await this.fetchUser();
    },

    async fetchUser() {
      const response = await api.get('/current-user');

      this.user = response.data.user;
      this.profiles = response.data.profiles;
      this.online_profile = response.data.online_profile;
      this.roles = response.data.roles;
      this.permissions = response.data.permissions;

      // Définir le thème selon le space
      if (this.online_profile?.space?.template) {
        this.theme = this.online_profile.space.template;
      }

      // Stockage du code profil
      Cookies.set('code', this.online_profile.type.code);
    },

    async switchProfile(profile_id, code) {
      await api.put('/change-space', { profile_id });

      Cookies.set('code', code);
      await this.fetchUser();

      // Redirection vers le bon domaine
      this.redirectToProfileDomain();
    },

    redirectToProfileDomain() {
      const profileCode = this.online_profile.type.code;
      const currentUrl = window.location.href;
      const targetUrl = spaceConfig[profileCode];

      if (targetUrl && !currentUrl.includes(targetUrl)) {
        if (import.meta.env.MODE === 'production') {
          window.location.href = targetUrl;
        } else {
          console.warn(`Should redirect to ${targetUrl}`);
        }
      }
    }
  }
});
```

### 5.4 Middleware de Redirection

**Fichier** : `src/router/middlewares/auth.js`

```javascript
export default async function auth({ to, from, next, router }) {
  const authStore = useAuthStore();
  const token = Cookies.get('token');

  // Vérifier authentification
  if (!token && to.path !== '/auth/login') {
    return next('/auth/login');
  }

  if (token && !authStore.user) {
    await authStore.fetchUser();
  }

  // Vérifier correspondance profil/domaine
  if (authStore.online_profile) {
    const profileCode = authStore.online_profile.type.code;
    const currentUrl = window.location.href;
    const targetUrl = spaceConfig[profileCode];

    // Redirection si mauvais domaine
    if (targetUrl && !currentUrl.includes(targetUrl)) {
      if (import.meta.env.MODE === 'production') {
        window.location.href = targetUrl;
        return;
      } else {
        console.warn(`Wrong domain! Should be on ${targetUrl}`);
      }
    }
  }

  next();
}
```

### 5.5 Layouts par Profil

**Sélection automatique du layout** :

```javascript
// router/index.js
{
  path: '/',
  component: () => import('@/views/Dashboard.vue'),
  meta: {
    layout: 'default',
    sidebar: redirectMappedLayout  // Fonction qui sélectionne le bon layout
  }
}
```

**Fonction de sélection** :

```javascript
function redirectMappedLayout() {
  const authStore = useAuthStore();
  const profileCode = authStore.online_profile?.type?.code;

  const layoutMap = {
    police: () => import('@/layouts/PoliceSidebar.vue'),
    interpol: () => import('@/layouts/InterpolSidebar.vue'),
    bank: () => import('@/layouts/BankSidebar.vue'),
    affiliate: () => import('@/layouts/AffiliateSidebar.vue'),
    // ... autres profils
  };

  return layoutMap[profileCode] || layoutMap.affiliate;
}
```

**Layouts disponibles** :
- `PoliceSidebar.vue` : Menu Police
- `InterpolSidebar.vue` : Menu Interpol
- `BankSidebar.vue` : Menu Banque
- `AffiliateSidebar.vue` : Menu générique affilié
- `CentralGarageSidebar.vue` : Menu Garage central
- `CourtSidebar.vue` : Menu Tribunal
- etc.

### 5.6 Fonctionnalités par Profil

#### Police
- Consultation véhicules
- Déclaration de vol
- Création d'opposition
- Recherche d'immatriculation
- Alertes véhicules recherchés
- Historique des consultations

#### Interpol
- Consultation véhicules internationaux
- Alertes véhicules volés
- Signalement international
- Statistiques

#### Banque
- Création de nantissement (pledge)
- Levée de nantissement
- Liste des véhicules nantis
- Historique des opérations

#### Garage Agréé
- Liste des véhicules en transformation
- Demandes de transformation
- Certificats de transformation

#### Commissaire Priseur
- Ventes aux enchères
- Gestion des véhicules en vente
- Historique des ventes

---

## 6. Flux Complets

### 6.1 Flow de Connexion (Toutes les Applications)

```
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 1 : Demande OTP                                       │
└─────────────────────────────────────────────────────────────┘

User saisit NPI (10 caractères)
         ↓
Frontend → POST /login/send-otp { npi: "1234567890" }
         ↓
Backend :
  1. Vérifie que User existe (username = NPI)
  2. Récupère données ANIP (nom, téléphone)
  3. Génère code OTP 4 chiffres
  4. Hash bcrypt du code
  5. Stocke en cache Redis (5 min)
  6. Envoie SMS via Vonage
  7. Envoie Email
         ↓
Frontend reçoit :
{
  "npi": "1234567890",
  "telephone": "***7890",
  "otp_duration": 5
}

┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 2 : Vérification OTP et Authentification OAuth2       │
└─────────────────────────────────────────────────────────────┘

User saisit code OTP
         ↓
Frontend → POST /login
{
  "username": "1234567890",
  "password": "1234",
  "grant_type": "password",
  "client_id": "<CLIENT_ID>",
  "client_secret": "<CLIENT_SECRET>"
}
         ↓
Backend :
  1. Récupère OTP hashé du cache
  2. Vérifie avec Hash::check(otp, cached_hash)
  3. Update temporaire User.password = Hash::make(otp)
  4. Génère token OAuth2 via Passport
  5. Set User.online_profile_id = premier profil type "user"
  6. Clear User.password
  7. Delete cache OTP
         ↓
Frontend reçoit :
{
  "token_type": "Bearer",
  "expires_in": 31536000,
  "access_token": "eyJ0eXAiOiJKV1Qi...",
  "refresh_token": "def502..."
}
         ↓
Frontend :
  1. Stocke token dans cookie (domain: .simveb-bj.com)
  2. Set authenticated = true
  3. Redirect vers "/"
```

### 6.2 Flow d'Inscription Personne Physique

```
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 1 : Initialisation                                    │
└─────────────────────────────────────────────────────────────┘

User saisit Email + NPI
         ↓
Frontend → POST /register/init
{
  "person_type": "physical",
  "npi": "1234567890",
  "email": "user@example.com"
}
         ↓
Backend :
  1. Valide NPI via ValideNpiRule
  2. Vérifie unicité (identities.npi, users.username)
  3. Vérifie unicité email (users.email)
  4. Appel ANIP : IdentityService->getIdentityByNpi()
  5. Cache données (30 minutes) :
     {
       "npi": "1234567890",
       "lastname": "DOE",
       "firstname": "John",
       "birthdate": "1990-01-01",
       "telephone": "+22912345678",
       "email": "user@example.com"
     }
  6. Génère OTP
  7. Envoie SMS
         ↓
Frontend reçoit :
{
  "npi": "1234567890",
  "telephone": "***7890",
  "otp_duration": 5
}

┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 2 : Vérification OTP                                  │
└─────────────────────────────────────────────────────────────┘

User saisit code OTP
         ↓
Frontend → POST /register/check-otp
{
  "person_type": "physical",
  "npi": "1234567890",
  "otp": "1234"
}
         ↓
Backend :
  1. Vérifie OTP via cache
  2. Récupère user_data en cache
         ↓
Frontend reçoit :
{
  "user_data": {
    "npi": "1234567890",
    "lastname": "DOE",
    "firstname": "John",
    "birthdate": "1990-01-01",
    "telephone": "+22912345678",
    "email": "user@example.com"
  }
}
         ↓
Frontend affiche formulaire adresse

┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 3 : Finalisation                                      │
└─────────────────────────────────────────────────────────────┘

User sélectionne adresse complète
         ↓
Frontend → POST /register/store
{
  "person_type": "physical",
  "npi": "1234567890",
  "state_id": 1,
  "town_id": 10,
  "district_id": 50,
  "village_id": 200,
  "house": "Maison blanche"
}
         ↓
Backend (Transaction DB) :
  1. Récupère user_data du cache
  2. Crée Identity :
     {
       "npi": "1234567890",
       "lastname": "DOE",
       "firstname": "John",
       "birthdate": "1990-01-01",
       "telephone": "+22912345678",
       "state_id": 1,
       "town_id": 10,
       "district_id": 50,
       "village_id": 200,
       "house": "Maison blanche"
     }
  3. Crée User :
     {
       "username": "1234567890",
       "email": "user@example.com",
       "identity_id": <IDENTITY_ID>
     }
  4. Crée Profile :
     {
       "user_id": <USER_ID>,
       "type_id": <TYPE_USER_ID>,
       "identity_id": <IDENTITY_ID>
     }
  5. Envoie notification succès (email + SMS)
  6. Delete cache
         ↓
Frontend reçoit :
{
  "success": true,
  "message": "Inscription réussie"
}
         ↓
Frontend redirect vers "/auth/login"
```

### 6.3 Flow d'Inscription Entreprise

```
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 1 : Initialisation                                    │
└─────────────────────────────────────────────────────────────┘

User saisit IFU
         ↓
Frontend → POST /register/init
{
  "person_type": "moral",
  "ifu": "1234567890123"
}
         ↓
Backend :
  1. Vérifie unicité IFU (institutions.ifu)
  2. Appel DGI : IdentityService->getIdentityByIfu()
  3. Cache company_data (30 minutes)
  4. Génère OTP
  5. Envoie EMAIL (pas SMS)
         ↓
Frontend reçoit OTP info

┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 2 : Vérification OTP                                  │
└─────────────────────────────────────────────────────────────┘

User saisit code OTP reçu par email
         ↓
Frontend → POST /register/check-otp
         ↓
Frontend reçoit company_data
         ↓
Frontend affiche formulaire documents

┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 3 : Finalisation                                      │
└─────────────────────────────────────────────────────────────┘

User saisit NPI admin + upload documents
         ↓
Frontend → POST /register/store (FormData)
{
  "person_type": "moral",
  "ifu": "1234567890123",
  "first_member_npi": "0987654321",
  "documents": [
    { "type_id": 1, "file": <FILE> },
    { "type_id": 2, "file": <FILE> }
  ]
}
         ↓
Backend (Transaction DB) :
  1. Vérifie NPI admin via ANIP
  2. Récupère company_data du cache
  3. Crée Institution
  4. Crée SpaceRegistrationRequest :
     {
       "institution_id": <INSTITUTION_ID>,
       "first_member_npi": "0987654321",
       "status": "pending"  ← EN ATTENTE
     }
  5. Upload documents
  6. Envoie notification entreprise
  7. Delete cache
         ↓
Frontend reçoit :
{
  "success": true,
  "message": "Demande enregistrée. En attente de validation."
}
         ↓
Frontend redirect vers "/auth/login"

┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 4 : Validation Admin (Backoffice)                     │
└─────────────────────────────────────────────────────────────┘

Admin backoffice valide la demande
         ↓
Backend :
  1. Crée Space pour l'institution
  2. Crée User pour le premier membre
  3. Crée Profile dans le Space
  4. Update SpaceRegistrationRequest.status = "approved"
  5. Envoie notification entreprise
         ↓
Entreprise peut maintenant se connecter
```

### 6.4 Flow de Changement de Profil

```
User connecté avec profil A
         ↓
User clique "Changer de profil"
         ↓
Frontend affiche liste des profils disponibles
         ↓
User sélectionne profil B
         ↓
Frontend → PUT /change-space
{
  "profile_id": "<PROFILE_B_ID>"
}
         ↓
Backend :
  1. Update User.online_profile_id = <PROFILE_B_ID>
  2. Vérifie que profil n'est pas suspendu
  3. Vérifie que space n'est pas suspendu
         ↓
Frontend :
  1. Update store avec nouveau profil
  2. Update cookie "code" avec nouveau profil code
  3. Redirect selon profil (pour Affiliate)
  4. Refresh page
```

---

## 7. Sécurité

### 7.1 Mécanismes de Sécurité

#### 🔐 OTP (One-Time Password)

**Caractéristiques** :
- Code à usage unique de 4 chiffres
- Durée de validité : 5 minutes (configurable)
- Stockage hashé (bcrypt) en cache Redis
- Envoi multi-canal : SMS + Email
- Invalidation automatique après utilisation

**Génération** :
```php
// Production
$code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

// Développement
$code = '1234';
```

**Stockage** :
```php
$key = $request->ip() . '-one-time-password';
$hashedOtp = Hash::make($code);

Cache::put($key, [
    'otp' => $hashedOtp,
    'npi' => $npi,
    'attempts' => 0
], $duration);
```

**Vérification** :
```php
$cached = Cache::get($key);

if (!Hash::check($otp, $cached['otp'])) {
    throw new Exception('Code OTP invalide');
}

Cache::forget($key);
```

#### 🔑 OAuth2 avec Laravel Passport

**Token JWT** :
- Signature cryptographique
- Expiration configurable
- Refresh token disponible
- Client credentials obligatoires

**Configuration** :
```php
// config/auth.php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
]
```

**Génération token** :
```php
$user = User::where('username', $npi)->first();

// Token via Passport
$tokenResult = $user->createToken('Personal Access Token');
$token = $tokenResult->accessToken;
```

#### ✅ Validation des Données

**Côté Backend (Laravel)** :
```php
// Form Request
class InitRegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'npi' => [
                'required',
                'string',
                'size:10',
                'unique:users,username',
                'unique:identities,npi',
                new ValideNpiRule()
            ],
            'email' => 'required|email|unique:users,email'
        ];
    }
}
```

**Côté Frontend (Vee-validate)** :
```javascript
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';

const schema = yup.object({
  npi: yup.string()
    .required('NPI requis')
    .length(10, 'NPI doit contenir 10 chiffres')
    .matches(/^[0-9]{10}$/, 'NPI invalide'),
  email: yup.string()
    .required('Email requis')
    .email('Email invalide')
});

const { handleSubmit } = useForm({ validationSchema: schema });
```

#### 🛡️ Protection des Routes

**Backend** :
```php
// Middleware sur routes API
Route::middleware(['auth:api', 'space.access'])->group(function () {
    Route::get('/current-user', [UserController::class, 'currentUser']);
    Route::get('/vehicles', [VehicleController::class, 'index']);
    // ...
});
```

**Frontend** :
```javascript
// Middleware global Nuxt
export default defineNuxtRouteMiddleware((to, from) => {
  const token = useCookie('token');

  if (!token.value && to.path !== '/auth/login') {
    return navigateTo('/auth/login');
  }
});
```

#### 🚫 Gestion des Suspensions

**Triple niveau de suspension** :

1. **User.disabled_at** : Compte suspendu
```php
if ($user->disabled_at) {
    return response()->json(['message' => 'Compte suspendu'], 403);
}
```

2. **Profile.suspended_at** : Profil suspendu
```php
if ($profile->suspended_at) {
    return response()->json(['message' => 'Profil suspendu'], 403);
}
```

3. **Space.status** : Espace suspendu
```php
if ($space->status !== 'active') {
    return response()->json(['message' => 'Espace suspendu'], 403);
}
```

#### 🌐 Intégration ANIP

**Vérification identité en temps réel** :

```php
class IdentityService
{
    public function getIdentityByNpi(string $npi): array
    {
        // Appel API ANIP via X-Road
        $response = $this->xroadClient->call('getPersonByNpi', [
            'npi' => $npi
        ]);

        return [
            'npi' => $response['npi'],
            'lastname' => $response['nom'],
            'firstname' => $response['prenoms'],
            'birthdate' => $response['date_naissance'],
            'telephone' => $response['telephone']
        ];
    }
}
```

#### 🔒 Cache Sécurisé

**Données sensibles** :
- Stockage temporaire en Redis
- Clé unique par IP + contexte
- Expiration automatique (5-30 minutes)
- Suppression après utilisation

```php
// Stockage
Cache::put($key, $data, $duration);

// Récupération et suppression
$data = Cache::get($key);
Cache::forget($key);
```

### 7.2 Best Practices Implémentées

✅ **Pas de mots de passe permanents** : Authentification par OTP uniquement
✅ **Tokens JWT signés** : OAuth2 standardisé
✅ **HTTPS obligatoire** : Toutes les communications chiffrées
✅ **CORS configuré** : Restrictions d'origine
✅ **Rate limiting** : Protection contre brute force
✅ **SQL injection** : Utilisation d'Eloquent ORM
✅ **XSS protection** : Sanitization automatique
✅ **CSRF protection** : Tokens CSRF pour forms web
✅ **Validation stricte** : Côté backend et frontend
✅ **Logs d'activité** : Activity log package

### 7.3 Recommandations Supplémentaires

🔶 **À ajouter** :
- Limitation de tentatives OTP (3-5 max)
- Blocage temporaire après échecs répétés
- 2FA optionnel pour admins
- Audit log des connexions
- Détection d'activité suspecte
- Rotation des tokens
- Password policy pour futurs mots de passe

---

## 8. Types d'Utilisateurs

### 8.1 Personne Physique (Citoyen)

**Profil** : `user` (Usager/Vendeur)

**Données collectées** :

| Champ | Source | Description |
|-------|--------|-------------|
| NPI | ANIP | Numéro Personnel d'Identification |
| Nom | ANIP | Nom de famille |
| Prénoms | ANIP | Prénoms |
| Date de naissance | ANIP | Date de naissance |
| Sexe | ANIP | Homme/Femme |
| Nationalité | ANIP | Nationalité |
| Téléphone | ANIP | Numéro de téléphone |
| Email | Utilisateur | Adresse email (saisie) |
| Département | Utilisateur | State (sélection) |
| Commune | Utilisateur | Town (sélection) |
| Arrondissement | Utilisateur | District (sélection) |
| Village/Quartier | Utilisateur | Village (sélection) |
| Maison | Utilisateur | Adresse précise (saisie) |

**Fonctionnalités** :
- Demande d'immatriculation
- Demande de mutation
- Demande de duplicata
- Demande de transformation
- Consultation de ses véhicules
- Suivi de ses demandes
- Paiement en ligne

### 8.2 Personne Morale (Entreprise)

**Profil** : `company` (Entreprise)

**Données collectées** :

| Champ | Source | Description |
|-------|--------|-------------|
| IFU | DGI | Identifiant Fiscal Unique |
| Raison sociale | DGI | Nom de l'entreprise |
| Siège social | DGI | Adresse |
| Téléphone | DGI | Téléphone entreprise |
| Email | DGI | Email entreprise |
| NPI admin | ANIP | Premier administrateur |
| Documents | Upload | Documents requis |

**Workflow** :
1. Inscription avec IFU
2. Upload documents
3. **Attente validation admin**
4. Validation → Création du Space
5. Accès au système

**Fonctionnalités** :
- Toutes les fonctionnalités citoyen
- Gestion multi-utilisateurs (invitations)
- Statistiques d'entreprise
- Historique complet

### 8.3 Institutions Affiliées

#### Police

**Profil** : `police`

**Fonctionnalités spécifiques** :
- Consultation véhicules sans restriction
- Déclaration de vol
- Création d'opposition
- Recherche d'immatriculation avancée
- Alertes véhicules recherchés
- Historique des consultations
- Export de données

#### Interpol

**Profil** : `interpol`

**Fonctionnalités spécifiques** :
- Consultation internationale
- Alertes véhicules volés
- Signalement international
- Coordination avec polices nationales
- Statistiques transfrontalières

#### Banque

**Profil** : `bank`

**Fonctionnalités spécifiques** :
- Création de nantissement
- Levée de nantissement
- Liste des véhicules nantis
- Validation par greffier
- Historique des opérations
- Export rapports

**Flow nantissement** :
1. Banque crée le pledge
2. Assignation à un greffier
3. Greffier valide ou rejette
4. Si validé : Vehicle.pledged = true
5. Levée possible par la banque

#### Garage Agréé

**Profil** : `approved`

**Fonctionnalités spécifiques** :
- Demandes de transformation
- Certificats de transformation
- Liste des véhicules en cours
- Validation technique
- Historique interventions

#### Commissaire Priseur

**Profil** : `auctioneer`

**Fonctionnalités spécifiques** :
- Gestion ventes aux enchères
- Ajout véhicules en vente
- Historique des ventes
- Transfert de propriété
- Rapports de vente

#### Gestionnaires (GMA/GMD)

**Profils** : `gma`, `gmd`

**Fonctionnalités spécifiques** :
- Gestion flotte gouvernementale
- Demandes spécifiques (plaques spéciales)
- Statistiques flotte
- Suivi des véhicules
- Export rapports

#### Tribunal

**Profil** : `court`

**Fonctionnalités spécifiques** :
- Consultation pour affaires judiciaires
- Création de saisies
- Levée de saisies
- Historique des décisions
- Export documents

### 8.4 Profils Multiples

**Concept** : Un utilisateur peut avoir plusieurs profils

**Exemple** :

```json
{
  "user": {
    "username": "1234567890",
    "email": "user@example.com"
  },
  "profiles": [
    {
      "id": "uuid-1",
      "type": "user",
      "space_id": null
    },
    {
      "id": "uuid-2",
      "type": "company",
      "space_id": "uuid-space-1",
      "space": {
        "name": "Entreprise SARL"
      }
    },
    {
      "id": "uuid-3",
      "type": "approved",
      "space_id": "uuid-space-2",
      "space": {
        "name": "Garage Certifié"
      }
    }
  ],
  "online_profile_id": "uuid-1"  // Profil actif
}
```

**Changement de profil** :
```javascript
// Frontend
await userSession.switchProfile(profile_id, code);

// Backend
PUT /change-space
{
  "profile_id": "uuid-2"
}

// Update User.online_profile_id
// Redirect si Affiliate (domaine spécifique)
```

---

## Annexes

### A. Récapitulatif des Endpoints

#### Authentification
```
POST   /login/send-otp
POST   /login/resend-otp
POST   /login
POST   /logout
GET    /current-user
PUT    /change-space
```

#### Inscription
```
POST   /register/init
POST   /register/resend-otp
POST   /register/check-otp
POST   /register/store
GET    /register/space-documents
```

#### Mot de passe
```
POST   /forgot-password
GET    /forgot-password/{token}
POST   /reset-password
PUT    /reset-password-expired
PUT    /update-password
```

#### Recherche géographique
```
GET    /registration/search/states
GET    /registration/search/towns?state_id={id}
GET    /registration/search/districts?town_id={id}
GET    /registration/search/villages?district_id={id}
```

#### Invitations
```
POST   /invitations
PUT    /invitations/{id}/validate
PUT    /invitations/{id}/deny
POST   /invitations/{id}/resend
```

### B. Variables d'Environnement

#### Backend (.env)
```bash
# Database
DB_CONNECTION=pgsql
DB_HOST=db
DB_DATABASE=simveb

# Passport
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=

# ANIP X-Road
XROAD_BASE_URL=https://common-ss.xroad.bj:8443
CHECK_NPI_URL=https://sandbox-api.simveb-bj.com/api/persons

# DGI
CHECK_IFU_URL=https://sandbox-api.simveb-bj.com/api/companies

# SMS
VONAGE_KEY=
VONAGE_SECRET=

# Novu
NOVU_SECRET_KEY=
```

#### Frontend (.env)
```bash
# API
VITE_API_URL=https://api.simveb-bj.com

# OAuth2
VITE_CLIENT_ID=<CLIENT_ID>
VITE_CLIENT_SECRET=<CLIENT_SECRET>

# Cookie
VITE_COOKIE_DOMAIN=.simveb-bj.com

# Sentry
VITE_SENTRY_DSN=
```

### C. Codes d'Erreur Courants

| Code | Message | Cause |
|------|---------|-------|
| 400 | NPI invalide | Format NPI incorrect |
| 400 | IFU invalide | Format IFU incorrect |
| 400 | Code OTP invalide | OTP incorrect ou expiré |
| 401 | Non authentifié | Token manquant ou invalide |
| 403 | Compte suspendu | User.disabled_at !== null |
| 403 | Profil suspendu | Profile.suspended_at !== null |
| 403 | Espace suspendu | Space.status !== 'active' |
| 404 | Utilisateur non trouvé | NPI n'existe pas |
| 409 | NPI déjà enregistré | Inscription déjà existante |
| 422 | Validation échouée | Données invalides |
| 429 | Trop de tentatives | Rate limit dépassé |
| 500 | Erreur serveur | Erreur interne |

### D. Checklist de Sécurité

**Avant mise en production** :

- [ ] Changer les OTP dev (supprimer "1234" hardcodé)
- [ ] Configurer HTTPS obligatoire
- [ ] Activer rate limiting sur /login
- [ ] Limiter tentatives OTP (3-5 max)
- [ ] Configurer CORS strictement
- [ ] Activer logs de sécurité
- [ ] Configurer Sentry
- [ ] Tester suspension de compte
- [ ] Tester suspension de profil
- [ ] Tester suspension d'espace
- [ ] Vérifier expiration des tokens
- [ ] Tester refresh tokens
- [ ] Audit des permissions
- [ ] Test d'intrusion
- [ ] Backup régulier de la base

---

**Document généré le:** 2025-12-08
**Version:** 1.0
**Projet:** SIMVEB - Modules d'Authentification et Inscription
