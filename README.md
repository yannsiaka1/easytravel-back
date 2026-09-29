# EasyTravel API

Backend Laravel de l'application académique **EasyTravel**. Il expose une API REST consommée par le frontend React/Vite.

## Fonctionnalités

- inscription et connexion par token Sanctum ;
- séparation des rôles `client` et `agent` ;
- recherche et gestion des voyages ;
- réservation avec contrôle des places disponibles ;
- simulation de paiement côté serveur ;
- génération de billets ;
- espace agent pour les voyages et les utilisateurs.

## Installation locale

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

L'API est alors disponible sur `http://localhost:8000/api`.

Comptes de démonstration créés uniquement hors production :

- `client@easytravel.test` / `password123`
- `agent@easytravel.test` / `password123`

## Déploiement

Voir [`DEPLOYMENT.md`](DEPLOYMENT.md) pour Railway/PostgreSQL.
