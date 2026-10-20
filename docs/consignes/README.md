# Organisation de l'équipe

| | Étudiant A — Conducteur | Étudiant B — Passager | Étudiant C — Avis & Admin |
|---|---|---|---|
| Contrôleurs | `TrajetController` | `ReservationController`, `ProfilController` | `AvisController`, `AdminController` |
| Modèles | `Trajet` | `Reservation` | `Avis` |
| Routes | `routes/module-a-conducteur.php` | `routes/module-b-passager.php` | `routes/module-c-avis.php` |
| Vues | `app/views/trajets/` | `app/views/reservations/`, `app/views/profil/` | `app/views/avis/`, `app/views/admin/` |
| Consignes | [etudiant-A.md](etudiant-A.md) | [etudiant-B.md](etudiant-B.md) | [etudiant-C.md](etudiant-C.md) |

Le **socle** (`app/core/`, `Utilisateur`, `AuthController`, gabarit, CSS, `routes/socle.php`, `database/`) est commun : on ne le modifie qu'après en avoir parlé à l'équipe.

## Contrats entre modules (à respecter dès le jour 1)

Ces méthodes sont utilisées par d'autres que leur propriétaire. Livrez d'abord la **signature** (avec un corps provisoire), puis le vrai code.

| Fournie par | Méthode | Utilisée par | Pour quand |
|---|---|---|---|
| C | `Avis::noteMoyenne(int $idUtilisateur): ?float` | A (demandes reçues), B (profil, recherche) | Déjà en bouchon (retourne `null`) |
| A | lecture d'un trajet par son id (objet `Trajet` ou `null`) | B (réserver), C (clôture, avis) | Lundi de la semaine 1 |
| A | `Trajet::reserverPlace(int $idPassager): bool` | A ou B selon votre décision (cf. conception §4) | Mi-semaine 1 |
| B | lecture des réservations d'un trajet | A (demandes reçues), C (qui peut noter ?) | Mi-semaine 1 |

Si vous avez besoin d'une méthode dans le modèle d'un autre : demandez-la, ou ajoutez-la dans **un commit séparé** et prévenez son propriétaire.

## Git

- Une branche par fonctionnalité : `a/publier-trajet`, `b/recherche`, `c/notation`…
- Messages : `feat(A): formulaire de publication`, `fix(B): doublon de réservation`, `docs(C): calcul note moyenne`.
- Petits commits fréquents **avec votre propre compte** (`git config user.name` / `user.email`) : l'historique par étudiant est noté.
- Avant de fusionner : `git pull origin main`, résoudre les conflits chez soi, tester, puis fusionner.
- Jamais de `config/config.php` dans Git (il est dans `.gitignore`).

## Règles communes de code

- **Toujours** des requêtes préparées (`prepare` + `execute` avec paramètres). Aucune variable concaténée dans du SQL.
- **Toujours** `e()` pour afficher une donnée dans une vue.
- **Jamais** d'id d'utilisateur venant d'un formulaire : l'utilisateur courant, c'est `Auth::id()`.
- **Toujours** vérifier que la ressource demandée appartient à l'utilisateur (sinon `$this->abort(403)`), même si le lien n'est pas affiché.
- Les formulaires POST contiennent `<?= Csrf::field() ?>` (sinon le routeur renvoie 419).
- Les statuts s'écrivent avec les constantes (`Trajet::STATUT_OUVERT`…), jamais en dur.
- Réutilisez les classes CSS existantes (`.trip`, `.route`, `.statut--…`, `.btn`, `.field`, `table`) et le composant `partials/route.php`.

## Comptes de test (`database/seed.sql`, mot de passe `CoRide2026!`)

| Compte | Situation utile pour tester |
|---|---|
| admin@coride.test | admin |
| lucas@… | conducteur des trajets 1 (Terminé) et 3 (Ouvert, 1 demande en attente, 1 refusée) |
| ines@… | conductrice du trajet 2 (Complet) ; passagère confirmée du trajet 1 |
| mehdi@… | conducteur du trajet 4 (Ouvert, aucune demande) ; passager confirmé du trajet 1 |
| sarah@… | confirmée sur le 2, en attente sur le 3 |
| tom@… | confirmé sur le 2, refusé sur le 3 |
