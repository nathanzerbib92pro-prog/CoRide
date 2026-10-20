# Étudiant A — Module « Conducteur »

**Vos fichiers** : `app/controllers/TrajetController.php`, `app/models/Trajet.php`, `app/views/trajets/`, `routes/module-a-conducteur.php`.
**Maquettes** : `docs/maquettes/04-publier-trajet`, `05-mes-trajets-demandes`.
Les routes existent déjà ; chaque méthode affiche un écran « à construire » que vous remplacez.

---

## Étape 0 — Le modèle `Trajet` (à faire en premier, B et C en dépendent)

1. Écrivez l'hydratation (tableau SQL → objet). Modèle à suivre : `Utilisateur::fromRow()`. Attention aux types : `places_restantes` arrive en chaîne depuis PDO.
2. Ajoutez les getters dont les vues auront besoin.
3. Écrivez une méthode qui retrouve un trajet par son id et renvoie `null` s'il n'existe pas. Poussez-la vite : B et C l'attendent.

> Question à vous poser : vos méthodes d'accès sont-elles `static` (« trouve-moi un trajet ») ou d'instance (« ce trajet, réserve une place ») ? Le sujet impose `reserverPlace()` en méthode d'instance.

## Étape 1 — Publier un trajet (`create` / `store`)

- `create()` affiche le formulaire (origine, destination, date/heure, places, prix indicatif). Inspirez-vous de `auth/register.php` pour afficher les erreurs champ par champ et ré-afficher les valeurs saisies.
- `store()` valide **côté serveur** (le HTML `required` ne protège de rien) :
  - champs non vides, départ ≠ arrivée (pensez à la casse : « paris » et « Paris ») ;
  - date valide **et dans le futur** — regardez `DateTime::createFromFormat()` et le format envoyé par un `<input type="datetime-local">` ;
  - nombre de places entier entre 1 et 8 — regardez `filter_var()` avec `FILTER_VALIDATE_INT` et ses `options` ;
  - prix vide ou nombre ≥ 0 (attention à la virgule française).
- À la création, que vaut `places_restantes` ? Quel statut initial ?
- Le conducteur, c'est `Auth::id()`. **Jamais** un champ caché du formulaire.
- Après succès : message flash + redirection (patron POST → Redirect → GET : pourquoi est-ce important si l'utilisateur rafraîchit la page ?).

## Étape 2 — Mes trajets (`mesTrajets`)

- Uniquement les trajets dont l'utilisateur connecté est le conducteur.
- Triez de façon utile (les prochains d'abord ? les terminés à la fin ?).
- Affichez le statut avec les classes `statut--ouvert`, `statut--complet`, `statut--termine`, et réutilisez `partials/route.php`.
- Chaque trajet mène à ses demandes reçues (`/mes-trajets/{id}`).

## Étape 3 — Demandes reçues (`demandes`)

- **Contrôle d'accès** : que se passe-t-il si Lucas tape l'URL d'un trajet d'Inès ? Il doit obtenir une 403 (`$this->abort(403)`), pas la liste.
- Il faut afficher le **nom** du passager, pas son id : quelle jointure ?
- Note moyenne de chaque passager : appelez `Avis::noteMoyenne()` (fournie par C). Tant qu'elle renvoie `null`, affichez « Pas encore noté » — votre vue doit déjà gérer ce cas.

## Étape 4 — Accepter / refuser (`accepterReservation`, `refuserReservation`) + `Trajet::reserverPlace()`

Avant de coder, mettez-vous d'accord avec B : **la place est-elle prise à la demande ou à l'acceptation ?** (voir `docs/conception/README.md` §4).

Points à traiter :
- la réservation existe-t-elle ? concerne-t-elle un trajet **dont vous êtes le conducteur** ? est-elle encore « En attente » ?
- `reserverPlace()` doit vérifier la disponibilité, décrémenter, et passer le trajet en « Complet » à 0. Elle renvoie `false` si ce n'est pas possible.
- Deux opérations doivent réussir **ensemble ou pas du tout** (statut de la réservation + compteur du trajet) : cherchez `beginTransaction()`, `commit()`, `rollBack()`.
- Cas de deux acceptations simultanées pour la dernière place : une condition dans le `WHERE` de votre `UPDATE` + `rowCount()` permet de savoir si la mise à jour a vraiment eu lieu. Réfléchissez-y.
- Ces actions sont en **POST** (formulaire avec `Csrf::field()`), jamais un simple lien.

## Étape 5 — Statistiques conducteur (`statistiques`)

- Nombre de trajets réalisés : définissez « réalisé » (statut Terminé ?).
- Passager le plus fréquent : une requête d'**agrégation** sur les réservations confirmées de vos trajets, regroupées par passager. Que faire en cas d'égalité ? Et s'il n'y a aucun passager ?

## Pièges fréquents

- Comparer des dates en chaînes au lieu de `DateTime`.
- Oublier de vérifier la propriété du trajet dans **chaque** action (pas seulement dans la liste).
- Écrire `'Complet'` en dur au lieu de `Trajet::STATUT_COMPLET`.

## Tests manuels avec le jeu de données

- Connecté en Lucas : `/mes-trajets` montre 2 trajets ; `/mes-trajets/3` montre Sarah (en attente) et Tom (refusé).
- Connecté en Inès : `/mes-trajets/3` → 403.
- Acceptez Sarah sur le trajet 3 : places 4 → 3, statut toujours Ouvert.
- Publiez un trajet avec une date passée, 0 place, ou départ = arrivée → messages d'erreur.

## Barème qui vous concerne

Module individuel 7 pts · Logique métier (décrément des places) 4 pts · Sécurité 3 pts · UX/erreurs 1 pt.
