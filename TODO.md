# TODO — AI SaaS Starter (Laravel)

## 0) Pré-setup
- [ ] Créer repo: `laravel-ai-saas-starter`
- [ ] Activer GitHub Actions (CI PHP + Node)
- [ ] Créer `.env.example` complet (Stripe, OpenAI, Redis, S3)
- [ ] Ajouter code of conduct + licence (MIT) + PR template

## 1) Skeleton & Auth (Jour 1)
- [ ] `laravel new` / Sail + Docker
- [ ] Breeze (Inertia + Vue 3 + TS + Tailwind)
- [ ] Dark mode + layout de base (Nav, Sidebar, Toast)
- [ ] Socialite (Google/GitHub), flux OAuth E2E
- [ ] Tests Pest : auth basique (register/login)

## 2) Domain & Drivers IA (Jour 2)
- [ ] Arborescence `app/Domain/Ai/{Drivers,Services}`
- [ ] `OpenAIDriver` (texte + image) avec timeouts/retry
- [ ] `TextGenerator` / `ImageGenerator` (services)
- [ ] Config coûts (token/credit) + conversion
- [ ] Tests unitaires services IA (mocks)

## 3) Billing & Crédits (Jour 3)
- [ ] Stripe + Cashier (plans Free/Pro/Team)
- [ ] Webhooks Stripe (checkout/subscription/invoice)
- [ ] `credits_ledger` + `CreditService` (transactions DB)
- [ ] Cron mensuel reset crédits
- [ ] Tests: webhooks, ledger atomique

## 4) Copy Studio (Jour 4)
- [ ] UI: prompt builder (persona/ton/longueur)
- [ ] Job queue + état en temps réel (polling/Echo)
- [ ] Historique + détail job (tokens/cost/duration)
- [ ] Templates CRUD (privé)
- [ ] Tests features (quota/erreurs)

## 5) Image Studio (Jour 5)
- [ ] UI: prompt + options (count/size)
- [ ] Génération 1–4 images, stockage S3
- [ ] Galerie + téléchargement
- [ ] Débit crédits images (coût > texte)
- [ ] Tests features

## 6) API Publique + Docs (Jour 6)
- [ ] Sanctum + `api_keys` (hash + last_used_at)
- [ ] Endpoints `POST /api/v1/generate-text`, `POST /api/v1/generate-image`, `GET /api/v1/jobs/:id`
- [ ] Rate limit par plan & par clé
- [ ] `openapi.yaml` + page `/developers`
- [ ] Tests E2E API (auth, quotas, 429)

## 7) Analytics, Audit, Polish (Jour 7)
- [ ] Dashboard usage (ApexCharts)
- [ ] `audit_logs` (actions sensibles)
- [ ] Pages légales (CGU/Privacy)
- [ ] README final avec screenshots/gif
- [ ] Seeders démo + `php artisan app:demo`

## CI/CD & Qualité (transversal)
- [ ] GitHub Actions: `composer validate`, Pint, PHPStan, Pest, `npm build`
- [ ] Larastan level max
- [ ] Docker compose (nginx, php-fpm, mysql, redis)
- [ ] Pre-commit hooks (lint-staged) option

## Nice-to-have (post-MVP)
- [ ] Webhooks utilisateur (`job.succeeded/failed`)
- [ ] Referrals (bonus crédits)
- [ ] Templates publics/marketplace
- [ ] i18n (fr/en)
- [ ] Multi-provider IA + failover
