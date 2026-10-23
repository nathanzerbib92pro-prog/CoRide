<?php
declare(strict_types=1);

/**
 * MODULE C — Avis & réputation. PROPRIÉTAIRE : Étudiant C.
 * Consignes détaillées : docs/consignes/etudiant-C.md
 */
final class AvisController extends Controller
{
    public const COMMENTAIRE_MAX = 1000;

    /** POST /trajets/{id}/cloturer */
    public function cloturer(int $id): void
    {
        $trajet = $this->trajetOu404($id);

        if ($trajet->getIdConducteur() !== Auth::id()) {
            $this->abort(403, 'Seul le conducteur peut clôturer ce trajet.');
        }
        if ($trajet->isTermine()) {
            Session::flash('info', 'Ce trajet est déjà terminé.');
        } elseif (!$trajet->isDepartPasse()) {
            Session::flash('error', 'Impossible de clôturer un trajet avant son heure de départ.');
        } else {
            $trajet->terminer();
            Session::flash('success', 'Trajet clôturé : vous pouvez maintenant noter vos passagers.');
        }
        $this->redirect('/trajets/' . $id . '/avis');
    }

    /** GET /trajets/{id}/avis */
    public function create(int $id): void
    {
        Trajet::cloturerTrajetsPasses();
        $this->afficherFormulaire($this->trajetOu404($id));
    }

    /** POST /trajets/{id}/avis */
    public function store(int $id): void
    {
        $trajet = $this->trajetOu404($id);
        $auteur = Auth::id();

        // Toutes les règles sont revérifiées ici : le formulaire POST peut être envoyé sans passer par la page.
        if (!$trajet->isTermine()) {
            Session::flash('error', 'Ce trajet n\'est pas encore terminé : la notation n\'est pas ouverte.');
            $this->redirect('/trajets/' . $id . '/avis');
        }

        $cibles = Avis::cibles($trajet, $auteur);
        if ($cibles === []) {
            $this->abort(403, 'Vous n\'étiez ni conducteur ni passager confirmé de ce trajet.');
        }

        $idCible = filter_var($this->input('id_cible'), FILTER_VALIDATE_INT);
        $cible = null;
        foreach ($cibles as $c) {
            if ($c['id'] === $idCible) {
                $cible = $c;
            }
        }
        if ($cible === null) {
            Session::flash('error', 'Vous ne pouvez pas noter cette personne sur ce trajet.');
            $this->redirect('/trajets/' . $id . '/avis');
        }
        if ($cible['deja_note']) {
            Session::flash('error', 'Vous avez déjà noté ' . $cible['nom'] . ' sur ce trajet.');
            $this->redirect('/trajets/' . $id . '/avis');
        }

        $errors = [];
        $note = filter_var($this->input('note'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => Avis::NOTE_MIN, 'max_range' => Avis::NOTE_MAX],
        ]);
        if ($note === false) {
            $errors['note'] = 'Choisissez une note entière de ' . Avis::NOTE_MIN . ' à ' . Avis::NOTE_MAX . '.';
        }
        $commentaire = $this->input('commentaire');
        if (mb_strlen($commentaire) > self::COMMENTAIRE_MAX) {
            $errors['commentaire'] = 'Le commentaire ne doit pas dépasser ' . self::COMMENTAIRE_MAX . ' caractères.';
        }

        if ($errors) {
            $this->afficherFormulaire($trajet, $errors, $cible['id'], ['note' => $this->input('note'), 'commentaire' => $commentaire]);
            return;
        }

        try {
            // Enregistré tel quel : l'échappement se fait à l'affichage avec e().
            Avis::creer($id, $auteur, $cible['id'], $note, $commentaire === '' ? null : $commentaire);
        } catch (PDOException $ex) {
            // Double envoi simultané : la contrainte UNIQUE de la base prend le relais.
            if ($ex->getCode() !== '23000') {
                throw $ex;
            }
            Session::flash('error', 'Vous avez déjà noté ' . $cible['nom'] . ' sur ce trajet.');
            $this->redirect('/trajets/' . $id . '/avis');
        }

        Session::flash('success', 'Merci ! Votre avis sur ' . $cible['nom'] . ' est publié.');
        $this->redirect('/trajets/' . $id . '/avis');
    }

    private function trajetOu404(int $id): Trajet
    {
        return Trajet::findById($id) ?? $this->abort(404, 'Ce trajet n\'existe pas.');
    }

    private function afficherFormulaire(Trajet $trajet, array $errors = [], ?int $cibleEnErreur = null, array $old = []): void
    {
        $idUser = Auth::id();
        $estConducteur = $trajet->getIdConducteur() === $idUser;
        $cibles = $trajet->isTermine() ? Avis::cibles($trajet, $idUser) : [];

        // Message de blocage affiché à la place du formulaire (null = on peut noter).
        $blocage = null;
        if (!$trajet->isTermine()) {
            $blocage = 'Ce trajet n\'est pas encore terminé : vous pourrez noter après le voyage.';
        } elseif ($cibles === []) {
            $blocage = $estConducteur
                ? 'Aucun passager confirmé sur ce trajet : personne à noter.'
                : 'Vous n\'étiez pas passager confirmé de ce trajet.';
        }

        $this->render('avis/create', [
            'trajet'        => $trajet,
            'cibles'        => $cibles,
            'blocage'       => $blocage,
            'peutCloturer'  => $estConducteur && !$trajet->isTermine() && $trajet->isDepartPasse(),
            'errors'        => $errors,
            'cibleEnErreur' => $cibleEnErreur,
            'old'           => $old,
        ], 'Noter votre trajet');
    }
}
