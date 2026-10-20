# CoRide

Application web de covoiturage entre étudiants : les conducteurs publient leurs trajets, les passagers réservent une place, et chacun se note après le voyage.
PHP 8 (POO) · MySQL/MariaDB · architecture MVC · hébergement AlwaysData.

## Démarrage en local

```bash
# 1. Base de données
mysql -u root -e "CREATE DATABASE coride CHARACTER SET utf8mb4"
mysql -u root coride < database/schema.sql
mysql -u root coride < database/seed.sql

# 2. Configuration
cp config/config.example.php config/config.php   # puis renseigner les identifiants

# 3. Serveur
php -S localhost:8000 -t public public/index.php
```

Comptes de test (mot de passe `CoRide2026!`) : `admin@`, `lucas@`, `ines@`, `mehdi@`, `sarah@`, `tom@` + `coride.test`.
Extensions PHP requises : `pdo_mysql`, `mbstring`.

## Arborescence

```
public/              Seul dossier exposé au web
  index.php          Point d'entrée unique (front controller)
  .htaccess          Redirige tout vers index.php
  assets/css/        Feuille de style commune
app/
  bootstrap.php      Config, autoload, session, gestion d'erreurs
  core/              Socle : Database (PDO), Router, Controller, Model, Auth, Session, Csrf, helpers
  models/            Utilisateur (socle), Trajet (A), Reservation (B), Avis (C)
  controllers/       Auth/Home/Error (socle), Trajet (A), Reservation + Profil (B), Avis + Admin (C)
  views/             Gabarit, écrans, composants (partials/route.php)
routes/              socle.php + un fichier par module
config/              config.example.php (config.php est ignoré par Git)
database/            schema.sql, seed.sql
bin/                 Scripts en ligne de commande (tâche planifiée éventuelle)
docs/
  conception/        MCD, MLD, diagramme de classes, cycle de vie, règles de gestion
  consignes/         Organisation de l'équipe + fiche de chaque étudiant
  gantt/             Fichier GanttProject + aperçu
  maquettes/         9 écrans SVG (import Figma) + PNG
  deploiement-alwaysdata.md
```

## Cycle d'une requête

`navigateur → .htaccess → public/index.php → Router (middlewares auth/admin + CSRF) → Contrôleur → Modèle (PDO) → Vue dans layout/main.php`

## Équipe

| Rôle | Module | Membre |
|---|---|---|
| Étudiant A | Conducteur | [Prénom Nom] |
| Étudiant B | Passager | [Prénom Nom] |
| Étudiant C | Avis & Admin | [Prénom Nom] |

Voir `docs/consignes/README.md` pour les règles Git et les contrats entre modules.
