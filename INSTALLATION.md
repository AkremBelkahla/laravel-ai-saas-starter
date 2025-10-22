# 🚀 Guide d'installation - Laravel AI SaaS Starter

## Prérequis

- **PHP** 8.3 ou supérieur
- **Composer** 2.x
- **Node.js** 20.x ou supérieur
- **Docker Desktop** (pour Laravel Sail)
- **Git**

## Installation rapide

### 1. Cloner le projet

```bash
git clone https://github.com/infinityweb/laravel-ai-saas-starter.git
cd laravel-ai-saas-starter
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurer les variables d'environnement

Éditer le fichier `.env` et configurer :

#### Base de données (via Sail)
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_ai_saas
DB_USERNAME=sail
DB_PASSWORD=password
```

#### OpenAI (REQUIS)
```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxx
OPENAI_ORGANIZATION=org-xxxxxxxxxxxxx  # Optionnel
```

Obtenir une clé : https://platform.openai.com/api-keys

#### Stripe (REQUIS pour billing)
```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxx
```

**Créer les prix dans Stripe Dashboard :**
1. Aller sur https://dashboard.stripe.com/test/products
2. Créer 3 produits avec abonnement mensuel :
   - **Free** : $0/mois
   - **Pro** : $29/mois
   - **Team** : $99/mois
3. Copier les Price IDs dans `.env` :

```env
STRIPE_PRICE_FREE=
STRIPE_PRICE_PRO=price_xxxxxxxxxxxxx
STRIPE_PRICE_TEAM=price_xxxxxxxxxxxxx
```

#### OAuth (Optionnel)

**Google OAuth :**
1. Aller sur https://console.cloud.google.com/
2. Créer un projet et activer Google+ API
3. Créer des identifiants OAuth 2.0
4. Ajouter `http://localhost/auth/google/callback` aux URIs de redirection

```env
GOOGLE_CLIENT_ID=xxxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxxxxxxxxxxx
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

**GitHub OAuth :**
1. Aller sur https://github.com/settings/developers
2. Créer une nouvelle OAuth App
3. Authorization callback URL : `http://localhost/auth/github/callback`

```env
GITHUB_CLIENT_ID=xxxxxxxxxxxxx
GITHUB_CLIENT_SECRET=xxxxxxxxxxxxx
GITHUB_REDIRECT_URI=http://localhost/auth/github/callback
```

#### AWS S3 (Optionnel - pour stockage images)
```env
AWS_ACCESS_KEY_ID=xxxxxxxxxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxx
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

Si non configuré, les images seront stockées localement.

### 5. Démarrer avec Docker Sail

```bash
# Démarrer les conteneurs
./vendor/bin/sail up -d

# Ou créer un alias (recommandé)
alias sail='./vendor/bin/sail'
sail up -d
```

### 6. Migrations et seed

```bash
sail artisan migrate
sail artisan app:demo
```

La commande `app:demo` crée :
- 3 utilisateurs de test (Free, Pro, Team)
- Templates de prompts
- Crédits initiaux

### 7. Compiler les assets

```bash
npm run dev
```

Pour la production :
```bash
npm run build
```

### 8. Configurer les webhooks Stripe (Production)

1. Installer Stripe CLI : https://stripe.com/docs/stripe-cli
2. Écouter les webhooks localement :

```bash
stripe listen --forward-to localhost/webhooks/stripe
```

3. Copier le webhook secret affiché dans `.env`

En production, configurer le webhook dans Stripe Dashboard :
- URL : `https://votre-domaine.com/webhooks/stripe`
- Événements : `customer.subscription.*`, `invoice.*`

### 9. Lancer Horizon (Queue worker)

```bash
sail artisan horizon
```

Ou en arrière-plan :
```bash
sail artisan horizon &
```

### 10. Accéder à l'application

- **Application** : http://localhost
- **Horizon** : http://localhost/horizon

## Comptes de test

| Email | Mot de passe | Plan |
|-------|--------------|------|
| demo@example.com | password | Free |
| pro@example.com | password | Pro |
| team@example.com | password | Team |

## Tests

```bash
# Tous les tests
sail test

# Tests spécifiques
sail artisan test --filter=AuthTest

# Avec couverture
sail test --coverage
```

## Commandes utiles

```bash
# Vider le cache
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear

# Recréer la base de données
sail artisan migrate:fresh --seed

# Lancer Pint (formatage code)
sail composer lint

# Lancer PHPStan (analyse statique)
sail composer analyse

# Générer des clés API
sail tinker
>>> $team = App\Models\Team::first();
>>> $user = $team->owner;
>>> $key = App\Models\ApiKey::generate($team, $user, 'My API Key');
>>> echo $key->key; // Sauvegarder cette clé !
```

## Résolution de problèmes

### Erreur : "SQLSTATE[HY000] [2002] Connection refused"

Les conteneurs Docker ne sont pas démarrés :
```bash
sail up -d
```

### Erreur : "Class 'OpenAI' not found"

Installer les dépendances :
```bash
sail composer install
```

### Erreur : "Vite manifest not found"

Compiler les assets :
```bash
npm run dev
```

### Les jobs ne se lancent pas

Vérifier que Horizon tourne :
```bash
sail artisan horizon
```

### Erreur Stripe : "No such price"

Vérifier que les Price IDs dans `.env` sont corrects et correspondent à votre compte Stripe.

### Permission denied sur les fichiers

```bash
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```

## Déploiement en production

### 1. Variables d'environnement

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Utiliser des clés Stripe live
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
```

### 2. Optimisations

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### 3. Cron job

Ajouter au crontab :
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Supervisor pour Horizon

Créer `/etc/supervisor/conf.d/horizon.conf` :
```ini
[program:horizon]
process_name=%(program_name)s
command=php /path-to-project/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path-to-project/storage/logs/horizon.log
stopwaitsecs=3600
```

Puis :
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon
```

## Support

- **Documentation** : Voir README.md
- **Issues** : https://github.com/infinityweb/laravel-ai-saas-starter/issues
- **Email** : akrem.belkahla@infinityweb.tn

## Licence

MIT License - Voir LICENSE pour plus de détails.
