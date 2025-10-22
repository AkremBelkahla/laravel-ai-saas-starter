# TODO — AI SaaS Starter (Laravel)

## 📊 Statut du projet

**Date de génération** : 22 octobre 2025  
**Statut global** : ✅ **MVP COMPLET** (95% terminé)  
**Prêt pour** : Développement local, tests, déploiement

### ✅ Fonctionnalités implémentées
- Architecture complète (Laravel 11 + Vue 3 + Inertia)
- Authentification (Breeze + OAuth Google/GitHub)
- Système de billing Stripe avec 3 plans
- Génération de texte (OpenAI GPT-4)
- Génération d'images (DALL-E 3)
- Système de crédits avec ledger
- API REST publique avec Sanctum
- Dashboard avec analytics
- Tests Pest
- CI/CD GitHub Actions
- Documentation complète

### 🚀 Prochaines étapes
1. Configurer les clés API (OpenAI, Stripe)
2. Tester les fonctionnalités
3. Ajouter pages légales (optionnel)
4. Déployer en production

---

## 0) Pré-setup
- [x] Créer repo: `laravel-ai-saas-starter`
- [x] Activer GitHub Actions (CI PHP + Node)
- [x] Créer `.env.example` complet (Stripe, OpenAI, Redis, S3)
- [x] Ajouter code of conduct + licence (MIT) + PR template

## 1) Skeleton & Auth (Jour 1)
- [x] `laravel new` / Sail + Docker
- [x] Breeze (Inertia + Vue 3 + TS + Tailwind)
- [x] Dark mode + layout de base (Nav, Sidebar, Toast)
- [x] Socialite (Google/GitHub), flux OAuth E2E
- [x] Tests Pest : auth basique (register/login)

## 2) Domain & Drivers IA (Jour 2)
- [x] Arborescence `app/Domain/Ai/{Drivers,Services}`
- [x] `OpenAIDriver` (texte + image) avec timeouts/retry
- [x] `TextGenerator` / `ImageGenerator` (services)
- [x] Config coûts (token/credit) + conversion
- [x] Tests unitaires services IA (mocks)

## 3) Billing & Crédits (Jour 3)
- [x] Stripe + Cashier (plans Free/Pro/Team)
- [x] Webhooks Stripe (checkout/subscription/invoice)
- [x] `credits_ledger` + `CreditService` (transactions DB)
- [x] Cron mensuel reset crédits
- [x] Tests: webhooks, ledger atomique

## 4) Copy Studio (Jour 4)
- [x] UI: prompt builder (persona/ton/longueur)
- [x] Job queue + état en temps réel (polling/Echo)
- [x] Historique + détail job (tokens/cost/duration)
- [x] Templates CRUD (privé)
- [x] Tests features (quota/erreurs)

## 5) Image Studio (Jour 5)
- [x] UI: prompt + options (count/size)
- [x] Génération 1–4 images, stockage S3
- [x] Galerie + téléchargement
- [x] Débit crédits images (coût > texte)
- [x] Tests features

## 6) API Publique + Docs (Jour 6)
- [x] Sanctum + `api_keys` (hash + last_used_at)
- [x] Endpoints `POST /api/v1/generate-text`, `POST /api/v1/generate-image`, `GET /api/v1/jobs/:id`
- [x] Rate limit par plan & par clé
- [x] `openapi.yaml` + page `/developers`
- [x] Tests E2E API (auth, quotas, 429)

## 7) Analytics, Audit, Polish (Jour 7)
- [x] Dashboard usage (Stats + Graphiques)
- [x] `audit_logs` (actions sensibles)
- [ ] Pages légales (CGU/Privacy)
- [x] README final avec screenshots/gif
- [x] Seeders démo + `php artisan app:demo`

## CI/CD & Qualité (transversal)
- [x] GitHub Actions: `composer validate`, Pint, PHPStan, Pest, `npm build`
- [x] Larastan level max
- [x] Docker compose (nginx, php-fpm, mysql, redis)
- [ ] Pre-commit hooks (lint-staged) option

## Nice-to-have (post-MVP)
- [ ] Webhooks utilisateur (`job.succeeded/failed`)
- [ ] Referrals (bonus crédits)
- [ ] Templates publics/marketplace
- [ ] i18n (fr/en)
- [ ] Multi-provider IA + failover
