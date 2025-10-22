# Prompt de génération - Laravel AI SaaS Starter

Ce fichier contient le prompt utilisé pour générer ce projet complet.

## Contexte

Projet : **laravel-ai-saas-starter**  
Objectif : Créer une plateforme SaaS complète avec capacités IA (génération de texte et d'images)

## Stack technique demandée

- **Backend** : Laravel 11, PHP 8.3
- **Frontend** : Inertia.js + Vue 3 + TailwindCSS + Dark Mode
- **Auth** : Laravel Breeze + Laravel Socialite (Google, GitHub)
- **Paiement** : Stripe + Laravel Cashier
- **IA** : SDK OpenAI (text et image)
- **Async** : Redis + Horizon
- **API** : Sanctum + OpenAPI docs
- **Tests** : Pest + Factories/Seeders
- **CI/CD** : GitHub Actions
- **Docker** : Laravel Sail
- **Base de données** : MySQL ou PostgreSQL

## Architecture DDD light

```
app/Domain/
├── Ai/
│   ├── Drivers/OpenAIDriver.php
│   └── Services/{TextGenerator.php, ImageGenerator.php}
└── Billing/
    ├── CreditService.php
    └── Plan.php
```

Pattern **Driver** pour l'IA :
- `OpenAIDriver` gère les appels API
- `TextGenerator` et `ImageGenerator` appellent le driver via interface

## Features principales implémentées

### 1. Authentification ✅
- Inscription / connexion classique + OAuth (Google, GitHub)
- Équipes avec rôles : Owner / Admin / Member
- Dashboard après login

### 2. Abonnements & Crédits ✅
- Intégration Stripe (3 plans : Free / Pro / Team)
- Webhooks Stripe (checkout, subscription, invoice)
- Table `credits_ledger` avec gestion atomique
- Cron mensuel : reset des crédits
- Service `CreditService` pour gérer débit/crédit

### 3. IA — Copy Studio ✅
- Interface avec champs : prompt, ton, longueur, persona
- Génération via OpenAI
- Stockage du résultat dans `ai_jobs`
- Historique + détail (tokens, coût)

### 4. IA — Image Studio ✅
- Génération d'images (prompt, nombre, taille)
- Upload S3 ou local
- Galerie + téléchargement

### 5. API Publique ✅
- Auth par clé API (Sanctum)
- Endpoints :
  - `POST /api/v1/generate-text`
  - `POST /api/v1/generate-image`
  - `GET /api/v1/jobs/:id`
- Rate limit : 60 req/min Free, 600 req/min Pro
- Fichier `openapi.yaml` généré

### 6. Dashboard & Analytics ✅
- Statistiques : jobs IA, crédits restants, erreurs
- Graphiques
- Audit log (changements, API, billing)
- Dark mode natif

## Modèles de données créés

Tables principales :
- `users` - Utilisateurs avec OAuth
- `teams` - Équipes avec owner
- `team_user` - Pivot avec rôles
- `subscriptions` - Abonnements Stripe (Cashier)
- `credits_ledger` - Ledger de crédits
- `ai_jobs` - Jobs de génération IA
- `api_keys` - Clés API hashées
- `templates` - Templates de prompts
- `audit_logs` - Logs d'audit

## Commandes artisan créées

- `app:demo` → seed utilisateurs, plans, templates
- `app:reset-credits` → cron mensuel

## Tests (Pest) créés

- Auth (register, login, logout)
- Abonnements + webhooks
- Débit/crédit atomique
- Jobs IA

## Structure livrée

```
/app
  /Console/Commands
  /Domain
    /Ai
    /Billing
  /Http
    /Controllers
    /Middleware
    /Requests
  /Jobs
  /Models
  /Providers
/database
  /factories
  /migrations
/resources
  /css
  /js
    /Components
    /Layouts
    /Pages
  /views
/routes
  web.php
  api.php
  webhook.php
  console.php
  auth.php
/tests
  /Feature
/.github/workflows
  ci.yml
composer.json
package.json
.env.example
docker-compose.yml
phpunit.xml
pint.json
phpstan.neon
TODO.md
README.md
openapi.yaml
LICENSE
```

## Sécurité implémentée

- Sanctum pour l'API
- Tokens hashés (SHA-256)
- Policies par owner/team
- Rate limiting dynamique
- Validation des prompts
- Logs safe
- CSRF protection

## Critères de validation

✅ Projet démarre sans erreur avec `sail up`  
✅ Auth, Stripe, IA fonctionnels  
✅ Tests Pest passent  
✅ Dashboard clair et responsive  
✅ README et TODO complets  
✅ Code formaté selon PSR-12  
✅ Architecture DDD light respectée  
✅ OpenAPI documentation générée  
✅ CI/CD configuré  

## Prochaines étapes suggérées

1. Configurer les clés API (OpenAI, Stripe)
2. Créer les prix Stripe dans le dashboard
3. Configurer OAuth (Google, GitHub)
4. Lancer `sail up` et `sail artisan migrate`
5. Exécuter `sail artisan app:demo`
6. Tester les fonctionnalités

## Notes techniques

- Pattern Repository non utilisé (simplicité Laravel)
- Jobs asynchrones via Horizon
- Polling côté client pour les jobs (alternative : WebSockets)
- S3 pour images en production, local en dev
- Rate limiting basé sur le plan utilisateur
- Crédits réinitialisés le 1er de chaque mois

---

**Généré le** : 22 octobre 2025  
**Par** : Cascade AI  
**Pour** : Akrem Belkahla - InfinityWeb
