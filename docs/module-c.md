# Module C — Avis, réputation et administration

## 1. Clôture des trajets

### Clôture manuelle — `POST /trajets/{id}/cloturer`
Bouton « Clôturer ce trajet » sur l'écran de notation (`/trajets/{id}/avis`), visible du seul conducteur une fois l'heure de départ passée. Le serveur revérifie :
- le trajet existe (sinon 404) ;
- l'utilisateur connecté en est le conducteur (sinon 403) ;
- le trajet n'est pas déjà « Terminé » ;
- l'heure de départ est atteinte (on ne note pas un trajet qui n'a pas eu lieu).

### Clôture automatique
`Trajet::cloturerTrajetsPasses()` passe à « Terminé », en une seule requête groupée, les trajets « Ouvert » ou « Complet » partis depuis plus de **24 h** (`Trajet::DELAI_CLOTURE_AUTO`). Le délai de 24 h laisse au conducteur le temps de clôturer lui-même à l'arrivée.

Les deux pistes du sujet sont utilisées :

| Piste | Avantages | Risques |
|---|---|---|
| Requête groupée au chargement de certaines pages (`/trajets/{id}/avis`, `/admin`) | Rien à configurer, marche en local | Ne tourne que si quelqu'un visite ces pages ; petite requête en plus à chaque visite |
| Script `bin/cloturer-trajets.php` lancé par une **tâche planifiée AlwaysData** (toutes les heures) | Indépendant des visites, statuts toujours à jour | À configurer sur l'hébergeur ; si la tâche tombe en panne, personne ne le voit |

Les deux appellent la même méthode, donc si l'une ne passe pas, l'autre prend le relais.

Tâche planifiée AlwaysData (Avancé › Tâches planifiées) : `php $HOME/coride/bin/cloturer-trajets.php`, toutes les heures. Le script refuse de s'exécuter depuis le web.

### Décision : demandes « En attente » d'un trajet terminé
Elles passent à **« Refusée »** (dans la même transaction que la clôture) : le trajet a eu lieu sans ces passagers, et ils ne peuvent pas noter.

## 2. Laisser un avis — `GET|POST /trajets/{id}/avis`

**Qui note qui** (décision d'équipe n°3) : le conducteur note chacun de ses passagers « Confirmée », et chaque passager confirmé note le conducteur. Les passagers ne se notent pas entre eux. `Avis::cibles()` construit la liste des personnes que l'utilisateur peut noter, en précisant celles déjà notées.

Contrôles côté serveur (`AvisController::store`), chacun avec un message :

| Règle | Contrôle |
|---|---|
| Le trajet existe et est « Terminé » | `Trajet::findById()`, `isTermine()` |
| L'auteur a participé (conducteur ou passager confirmé) | `Avis::cibles()` vide → 403 |
| La cible a participé de l'autre côté et n'est pas l'auteur | la cible doit être dans `Avis::cibles()` |
| Pas déjà noté | `Avis::dejaNote()` + contrainte `UNIQUE` en base (exception `23000` interceptée) |
| Note entière de 1 à 5 | `filter_var(FILTER_VALIDATE_INT, min_range/max_range)` : `"4.5"`, `"5abc"`, `0`, `6` refusés |
| Commentaire ≤ 1000 caractères | `mb_strlen` (les accents comptent pour 1) |

Le commentaire est enregistré **tel quel** et échappé à l'affichage avec `e()`. Si on l'échappait avant l'INSERT, la base contiendrait du HTML (`&lt;`), ce qui fausserait la limite de 1000 caractères et donnerait un double échappement à l'affichage. Et si on oubliait `e()` un jour, on ne serait de toute façon pas protégé partout.

## 3. Note moyenne — `Avis::noteMoyenne(int $idUtilisateur): ?float`

```sql
SELECT AVG(note) FROM avis WHERE id_cible = :id
```
- `AVG` calcule la moyenne des notes **reçues**.
- Sans aucun avis, `AVG` renvoie `NULL` : la méthode renvoie `null` (« Pas encore noté »), à ne pas confondre avec une moyenne de 0.
- Pas d'arrondi dans la méthode : il se fait à l'affichage (`number_format($note, 1, ',', ' ')`, 4,5).
- La moyenne n'est stockée nulle part : elle est recalculée à chaque lecture. Une suppression par l'admin est donc prise en compte immédiatement.

Jeu de données : Lucas 4,5 (5 et 4) · Inès 5 · Mehdi 3 · Sarah, Tom : pas encore noté.

## 4. Administration (`/admin`, middleware `admin`)

### Statistiques (`Statistiques`)
- **Trajets réalisés** : nombre de trajets « Terminé ».
- **Taux de remplissage moyen** : pour chaque trajet terminé, `passagers « Confirmée » ÷ places_totales`, puis moyenne de ces taux (chaque trajet pèse autant, quelle que soit sa taille). On compte les réservations confirmées plutôt que `places_totales - places_restantes`, pour ne pas dépendre du moment où le module A décrémente le compteur.
  Vérification à la main : trajet 1, 2 confirmées sur 3 places → 2/3 = **67 %**.
- **Comptes actifs** (`actif = 1`) et **avis publiés**.

### Modération des avis
Liste de tous les avis (auteur → cible, note, commentaire échappé, trajet, date). La suppression se fait en **POST + jeton CSRF + confirmation**, et la page de retour est choisie dans une liste blanche.

### Gestion des comptes
- Désactiver plutôt que supprimer : les trajets, réservations et avis du compte y sont liés par clés étrangères. Une suppression échouerait, ou effacerait l'historique des autres.
- La connexion refuse déjà les comptes désactivés, et `Auth::user()` déconnecte une session en cours.
- Un admin ne peut pas se désactiver lui-même : il perdrait l'accès, et il pourrait ne plus rester aucun admin.

## 5. Tests manuels (jeu de données)

| Scénario | Résultat attendu | OK |
|---|---|---|
| Inès note Lucas sur le trajet 1 | « déjà noté » | ✅ |
| Sarah sur le trajet 1 | refus : pas passagère (403 en POST) | ✅ |
| Noter sur le trajet 3 (Ouvert) | refus : pas terminé | ✅ |
| Lucas clôture le trajet 3 avant le départ | refus | ✅ |
| Mehdi clôture le trajet de Lucas | 403 | ✅ |
| Notes `4.5`, `5abc`, `0`, `6`, vide | refus | ✅ |
| Commentaire de 1001 caractères | refus | ✅ |
| Commentaire `<script>` | enregistré tel quel, affiché échappé | ✅ |
| Conducteur clôture après le départ, puis note ses 2 passagers ; passager note le conducteur | OK, demandes en attente → Refusée | ✅ |
| Trajet parti depuis plus de 24 h | clôturé automatiquement (page et script) | ✅ |
| `/admin` sans être admin | 403 (GET et POST) | ✅ |
| Tableau de bord | 1 trajet réalisé · 67 % · 6 comptes actifs · 4 avis | ✅ |
| Suppression d'un avis sans jeton CSRF | 419 | ✅ |
| Désactiver son propre compte | refus | ✅ |
| Désactiver Tom puis se connecter en Tom | « Ce compte a été désactivé » | ✅ |

## 6. Points d'intégration avec A et B

- **A** : sur « Mes trajets », ajouter pour chaque trajet un lien « Clôturer / Noter » vers `/trajets/{id}/avis`. Les méthodes `fromRow`, getters et `findById` ont été ajoutées à `Trajet` par C (commit séparé, issue #6), à garder ou adapter.
- **B** : sur « Mes réservations », ajouter un lien « Noter » vers `/trajets/{id}/avis` pour les réservations confirmées d'un trajet terminé. Le profil public affiche `Avis::noteMoyenne()`.
