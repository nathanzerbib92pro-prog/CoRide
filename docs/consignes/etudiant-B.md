# Étudiant B — Module « Passager »

**Vos fichiers** : `app/controllers/ReservationController.php`, `app/controllers/ProfilController.php`, `app/models/Reservation.php`, `app/views/reservations/`, `app/views/profil/`, `routes/module-b-passager.php`.
**Maquettes** : `docs/maquettes/03-recherche-trajets`, `06-mes-reservations`, `07-profil-public`.

---

## Étape 0 — Le modèle `Reservation`

Hydratation + getters (modèle : `Utilisateur::fromRow()`), puis les méthodes dont vous aurez besoin. A vous demandera « les réservations d'un trajet » : prévoyez-la tôt.

## Étape 1 — Rechercher un trajet (`recherche`)

- Formulaire en **GET** (`/trajets?depart=Paris&arrivee=&date=`) : pourquoi GET et pas POST pour une recherche ? (pensez au bouton « précédent » et au partage d'URL)
- Seulement les trajets « Ouvert » et pas encore partis.
- Les trois filtres sont **facultatifs** : la requête change selon ce qui est rempli. Piste : construire une liste de conditions SQL et un tableau de paramètres en parallèle, puis assembler. Les **valeurs** passent toujours par des paramètres liés, jamais dans la chaîne SQL.
- Recherche « contient » sur les villes : où se placent les `%` du `LIKE` — dans le SQL ou dans la valeur du paramètre ?
- Filtre date : l'utilisateur donne un jour, la colonne contient un jour **et** une heure.
- Pensez à ré-afficher les filtres saisis dans le formulaire.
- Cette méthode de recherche vit dans le modèle `Trajet` (propriétaire : A) : prévenez-le et faites un commit séparé.

## Étape 2 — Réserver une place (`reserver`)

Les quatre règles du sujet, chacune avec un message clair :
1. le trajet existe et est « Ouvert » ;
2. il reste au moins une place ;
3. l'utilisateur n'est pas le conducteur ;
4. l'utilisateur n'a pas déjà demandé ce trajet.

Pistes :
- Où vérifier ces règles : contrôleur ou modèle ? Argumentez votre choix dans la doc.
- La base refuse déjà les doublons (`UNIQUE`). Si deux clics arrivent en même temps, PDO lève une `PDOException` : comment la reconnaître (regardez son code SQLSTATE) et la transformer en message pour l'utilisateur ?
- Statut initial de la demande ? La place est-elle décomptée maintenant ou à l'acceptation ? **Décision commune avec A.**
- Côté affichage (recherche) : désactivez ou masquez le bouton quand la réservation est impossible, mais **gardez les contrôles serveur** (un bouton masqué n'empêche pas un POST forgé).

## Étape 3 — Mes réservations (`mesReservations`)

- Uniquement les demandes de `Auth::id()`.
- Affichez le trajet (villes, date) et le conducteur : quelles jointures ?
- Badges `statut--en-attente`, `statut--confirmee`, `statut--refusee`.
- Si le trajet est « Terminé » et la réservation confirmée : lien vers la notation de C (`/trajets/{id}/avis`).

## Étape 4 — Profil public (`ProfilController::show`)

- Nom, note moyenne (`Avis::noteMoyenne()` de C — gérez le `null` : « Pas encore noté »), nombre de trajets réalisés.
- Définissez « trajets réalisés » avec l'équipe (conducteur seulement ? passager confirmé aussi ?) et écrivez-le dans la doc.
- Profil inexistant → 404. **N'affichez pas l'email** : c'est un profil public.
- Formatez la note (1 décimale, virgule française) : regardez `number_format()`.

## Pièges fréquents

- Concaténer `$_GET['depart']` dans le SQL « parce que c'est juste une recherche ».
- Ne vérifier le doublon qu'en PHP (course entre deux requêtes) ou qu'en SQL (message illisible).
- Afficher toutes les réservations au lieu de celles de l'utilisateur.

## Tests manuels avec le jeu de données

- Recherche sans filtre : trajets 3 et 4 seulement (le 2 est Complet, le 1 Terminé).
- Connecté en Sarah : réserver le trajet 3 → « déjà demandé ». Le trajet 4 → OK.
- Connecté en Lucas : réserver le trajet 3 → refus (c'est son trajet).
- Forcez un POST sur le trajet 2 (Complet) avec l'outil réseau du navigateur → refus.
- `/profil/2` (Lucas) : note 4,5 une fois le calcul de C en place.

## Barème qui vous concerne

Module individuel 7 pts · Logique métier 4 pts · Sécurité (injections SQL, accès) 3 pts · UX/erreurs 1 pt.
