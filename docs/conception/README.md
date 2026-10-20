# Conception — CoRide

Fichiers : `mcd.png`, `mld.png`, `classes.png`, `cycle-de-vie.png` (+ versions `.svg` et sources Graphviz `.dot`).
Pour régénérer une image : `dot -Tpng -Gdpi=150 mld.dot -o mld.png`.

## 1. Règles de gestion

| # | Règle | Où elle est garantie |
|---|---|---|
| RG1 | Un utilisateur peut publier 0 à n trajets ; un trajet a exactement 1 conducteur. | FK `trajets.id_conducteur` |
| RG2 | Un utilisateur peut être conducteur sur un trajet et passager sur un autre. | Pas de rôle « conducteur » en base |
| RG3 | Un passager ne peut pas réserver deux fois le même trajet. | `UNIQUE (id_trajet, id_passager)` + contrôle PHP (message clair) |
| RG4 | Un conducteur ne peut pas réserver une place sur son propre trajet. | Contrôle PHP (module B) |
| RG5 | Impossible de réserver un trajet dont `places_restantes = 0`. | Contrôle PHP + `UNSIGNED` (ne descend jamais sous 0) |
| RG6 | `places_restantes` est compris entre 0 et `places_totales` (1 à 8). | `CHECK chk_trajets_places` |
| RG7 | Un avis n'est possible que sur un trajet « Terminé ». | Contrôle PHP (module C) |
| RG8 | Un utilisateur ne note qu'une fois une même personne sur un même trajet. | `UNIQUE (id_trajet, id_auteur, id_cible)` |
| RG9 | La note est un entier de 1 à 5 ; on ne peut pas se noter soi-même. | `CHECK chk_avis_note`, `CHECK chk_avis_pas_soi_meme` |
| RG10 | Un utilisateur ne voit que ses trajets et réservations ; seul l'admin modère. | Middlewares `auth` / `admin` + contrôles de propriété dans chaque contrôleur |

> La base est un **filet de sécurité** : si une contrainte saute, PDO lève une exception. Le PHP doit vérifier **avant** pour afficher un message compréhensible.

## 2. Dictionnaire de données

| Table | Colonne | Type | Remarque |
|---|---|---|---|
| utilisateurs | id | INT UNSIGNED, PK, AI | |
| | nom | VARCHAR(100) | prénom + nom |
| | email | VARCHAR(190), UNIQUE | stocké en minuscules |
| | password | VARCHAR(255) | **hash** `password_hash()` |
| | role | ENUM('user','admin') | |
| | actif | TINYINT(1) | 0 = compte désactivé par l'admin |
| | created_at | DATETIME | |
| trajets | id | INT UNSIGNED, PK, AI | |
| | origine, destination | VARCHAR(120) | |
| | date_heure | DATETIME | départ |
| | prix | DECIMAL(6,2) NULL | prix indicatif par passager (ajouté : demandé par le formulaire du sujet) |
| | places_totales | TINYINT UNSIGNED | 1 à 8 |
| | places_restantes | TINYINT UNSIGNED | 0 à places_totales |
| | statut | ENUM('Ouvert','Complet','Terminé') | |
| | id_conducteur | FK → utilisateurs.id | |
| reservations | id | INT UNSIGNED, PK, AI | |
| | statut | ENUM('En attente','Confirmée','Refusée') | |
| | date_reservation | DATETIME | |
| | id_trajet | FK → trajets.id | suppression en cascade |
| | id_passager | FK → utilisateurs.id | |
| avis | id | INT UNSIGNED, PK, AI | |
| | note | TINYINT UNSIGNED | 1 à 5 |
| | commentaire | VARCHAR(1000) NULL | |
| | date | DATETIME | |
| | id_trajet | FK → trajets.id | |
| | id_auteur, id_cible | FK → utilisateurs.id | |

## 3. Du MCD au MLD

- `PUBLIER` (0,n — 1,1) → la clé de UTILISATEUR migre dans TRAJET : `#id_conducteur`.
- `RÉSERVER` (0,n — 0,n), porteuse de `statut` et `date_reservation` → devient la table `reservations`. On lui donne un `id` technique (pratique pour les URL `/reservations/{id}/accepter`) **et** une contrainte `UNIQUE (id_trajet, id_passager)` qui garde le sens de l'association.
- `ÉCRIRE`, `RECEVOIR`, `CONCERNER` (toutes 1,1 côté AVIS) → trois clés étrangères dans `avis`.

**MLD textuel**

- utilisateurs (<u>id</u>, nom, email, password, role, actif, created_at)
- trajets (<u>id</u>, origine, destination, date_heure, prix, places_totales, places_restantes, statut, #id_conducteur)
- reservations (<u>id</u>, statut, date_reservation, #id_trajet, #id_passager)
- avis (<u>id</u>, note, commentaire, date, #id_trajet, #id_auteur, #id_cible)

## 4. Décisions d'équipe à acter (et à écrire ici une fois tranchées)

1. **Quand décrémente-t-on `places_restantes` ?** À l'envoi de la demande (B) ou à l'acceptation par le conducteur (A) ? Le sujet dit « une place acceptée décrémente le compteur » : choisissez, notez-le, et faites en sorte que `Trajet::reserverPlace()` ne soit appelée qu'à un seul endroit.
2. **Que deviennent les demandes « En attente » quand un trajet passe « Complet » ou « Terminé » ?**
3. **Qui note qui ?** Interprétation retenue par défaut : conducteur ↔ chaque passager confirmé (pas passager ↔ passager).
4. **« Trajets réalisés » d'un profil** : comme conducteur seulement, ou aussi comme passager confirmé ?
