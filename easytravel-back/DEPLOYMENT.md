# Déploiement du backend EasyTravel sur Railway

Le backend est une API Laravel. Le frontend React est déployé séparément sur Vercel.

## 1. Ajouter PostgreSQL

Dans le projet Railway, créer un service PostgreSQL. Le service Laravel utilisera la variable `DATABASE_URL` exposée par PostgreSQL.

## 2. Variables du service Laravel

Variables minimales :

```text
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://VOTRE_BACKEND.up.railway.app
FRONTEND_URL=https://VOTRE_FRONTEND.vercel.app
LOG_CHANNEL=stderr
DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
RAILPACK_PHP_EXTENSIONS=pdo_pgsql
COMPOSER_NO_DEV=1
```

Générer `APP_KEY` localement avec :

```bash
php artisan key:generate --show
```

`RAILPACK_PHP_EXTENSIONS=pdo_pgsql` force l'installation du pilote PostgreSQL dans l'image PHP Railpack. `COMPOSER_NO_DEV=1` évite d'embarquer les dépendances de développement dans l'image de production. Ne pas conserver SQLite comme base de production.

## 3. Compte agent initial

Le seeder ne crée aucun compte de démonstration avec un mot de passe connu en production. Pour provisionner le premier agent lors du seeding Railway, ajouter au minimum :

```text
EASYTRAVEL_AGENT_EMAIL=adresse@example.com
EASYTRAVEL_AGENT_PASSWORD=un-mot-de-passe-solide
```

Les variables `EASYTRAVEL_AGENT_NOM`, `EASYTRAVEL_AGENT_PRENOM`, `EASYTRAVEL_AGENT_CNI` et `EASYTRAVEL_AGENT_TELEPHONE` sont facultatives.

## 4. Vérifications après déploiement

- `/` doit retourner `EasyTravel API` et `status: ok` ;
- `/up` doit répondre correctement au health check Laravel ;
- le frontend doit utiliser `https://VOTRE_BACKEND.up.railway.app/api` ;
- les logs ne doivent plus mentionner `database/database.sqlite`.
