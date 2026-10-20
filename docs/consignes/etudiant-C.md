# Étudiant C — Module « Avis & Réputation » + Administration

**Vos fichiers** : `app/controllers/AvisController.php`, `app/controllers/AdminController.php`, `app/models/Avis.php`, `app/views/avis/`, `app/views/admin/`, `routes/module-c-avis.php`, et éventuellement `bin/`.
**Maquettes** : `docs/maquettes/08-laisser-avis`, `09-admin`.

---

## Étape 0 — Priorité : le contrat `Avis::noteMoyenne()`

A et B l'appellent déjà ; elle renvoie `null` pour l'instant. **Ne changez jamais sa signature.** Remplacez le corps dès que possible (étape 3).

## Étape 1 — Clôture de trajet (`cloturer` + automatique)

- **Manuelle** : le conducteur clôture son trajet. Vérifiez que c'est bien le sien, qu'il est « Ouvert » ou « Complet », et que l'heure de départ est atteinte (sinon on pourrait noter un trajet qui n'a pas eu lieu).
- **Automatique** : un trajet dont la date est passée doit devenir « Terminé » sans intervention. Deux pistes, à comparer dans votre doc :
  1. une seule requête de mise à jour groupée, lancée à un moment où l'application tourne (début de certaines pages ?) ;
  2. un petit script PHP en ligne de commande dans `bin/`, lancé par une **tâche planifiée AlwaysData** (Avancé › Tâches planifiées).
  Quels sont les avantages et les risques de chacune ?
- Que deviennent les demandes encore « En attente » d'un trajet terminé ? (décision d'équipe)
- La connexion règle déjà l'heure MySQL sur celle de PHP (`Database::get()`) : `NOW()` est donc fiable.

## Étape 2 — Laisser un avis (`create` / `store`)

Conditions à vérifier côté serveur, chacune avec un message :
- le trajet existe et est « Terminé » ;
- l'auteur a participé : conducteur, **ou** passager avec réservation « Confirmée » ;
- la cible a participé de l'autre côté (conducteur ↔ passager) et n'est pas l'auteur ;
- l'auteur n'a pas déjà noté cette cible sur ce trajet (la contrainte `UNIQUE` existe aussi en base : gérez l'exception) ;
- la note est un **entier** de 1 à 5 (`filter_var` + options `min_range` / `max_range`), le commentaire ≤ 1000 caractères.

Pour l'écran : un passager note un seul conducteur ; un conducteur peut avoir plusieurs passagers à noter. Comment construire la liste « personnes que je peux encore noter » ?

Le commentaire est enregistré tel quel et **échappé à l'affichage** avec `e()` (pas de `htmlspecialchars` avant l'INSERT : pourquoi ?).

## Étape 3 — Calcul de la réputation (`Avis::noteMoyenne`)

- La base sait calculer une moyenne : quelle fonction d'agrégation ?
- Que renvoie-t-elle quand il n'y a **aucun** avis ? Distinguez « pas d'avis » (`null`) de « moyenne de 0 ».
- Arrondi : dans la méthode ou à l'affichage ? (B affiche avec une virgule.)
- Expliquez le calcul dans la documentation (c'est un livrable).

## Étape 4 — Espace admin (`AdminController`)

Le middleware `admin` bloque déjà les non-admins (testez-le !).
- **Statistiques globales** : nombre de trajets réalisés ; **taux de remplissage moyen**. Écrivez votre formule dans la doc (ex. : places occupées ÷ places proposées, sur quels trajets ?). Avec le jeu de données, vérifiez à la main que vous obtenez le bon chiffre.
- **Modération** : liste des avis (auteur, cible, note, commentaire, trajet) et suppression. Suppression en POST + CSRF + confirmation. La moyenne se met-elle à jour toute seule ? Pourquoi ?
- **Gestion des comptes** : liste des utilisateurs. La colonne `actif` existe et la connexion refuse déjà les comptes désactivés. Pourquoi désactiver plutôt que supprimer, vu les clés étrangères ? Un admin peut-il se désactiver lui-même ? Ajoutez vos routes d'action dans `routes/module-c-avis.php`.

## Pièges fréquents

- Vérifier « Terminé » dans la vue seulement (le formulaire POST reste attaquable).
- Accepter une note `"4.5"` ou `"5abc"`.
- Une moyenne qui plante ou affiche 0 pour un nouvel utilisateur.

## Tests manuels avec le jeu de données

- Lucas a 2 avis (5 et 4) → 4,5. Inès : 5. Mehdi : 3. Sarah et Tom : aucun.
- Connecté en Inès : noter Lucas sur le trajet 1 → « déjà noté ».
- Connecté en Sarah : noter sur le trajet 1 → refus (pas passagère).
- Noter sur le trajet 3 (Ouvert) → refus.
- Admin : taux de remplissage du trajet 1 = 2 places prises sur 3.

## Barème qui vous concerne

Module individuel 7 pts · Logique métier (clôture, note moyenne) 4 pts · Sécurité (accès admin) 3 pts · UX/erreurs 1 pt.
