<?php
declare(strict_types=1);

/**
 * MODULE C — Espace admin. PROPRIÉTAIRE : Étudiant C.
 * Le middleware 'admin' (routes/module-c-avis.php) bloque déjà les non-admins.
 */
final class AdminController extends Controller
{
    /** Nombre d'avis récents affichés sur le tableau de bord. */
    private const AVIS_TABLEAU_DE_BORD = 5;

    /** GET /admin */
    public function dashboard(): void
    {
        Trajet::cloturerTrajetsPasses();
        $this->render('admin/dashboard', [
            'trajetsRealises' => Statistiques::trajetsRealises(),
            'tauxRemplissage' => Statistiques::tauxRemplissageMoyen(),
            'comptesActifs'   => Statistiques::comptesActifs(),
            'nbAvis'          => Avis::compter(),
            'avis'            => array_slice(Avis::listerPourModeration(), 0, self::AVIS_TABLEAU_DE_BORD),
        ], 'Administration');
    }

    /** GET /admin/avis */
    public function avis(): void
    {
        $this->render('admin/avis', ['avis' => Avis::listerPourModeration()], 'Modération des avis');
    }

    /** POST /admin/avis/{id}/supprimer */
    public function supprimerAvis(int $id): void
    {
        if (Avis::supprimer($id)) {
            Session::flash('success', 'Avis supprimé. La note moyenne de la personne est recalculée automatiquement.');
        } else {
            Session::flash('error', 'Cet avis n\'existe plus.');
        }
        // Retour à la page d'origine (liste blanche : pas de redirection vers une URL fournie par le client).
        $this->redirect($this->input('retour') === '/admin' ? '/admin' : '/admin/avis');
    }

    /** GET /admin/utilisateurs */
    public function utilisateurs(): void
    {
        $this->render('admin/utilisateurs', ['utilisateurs' => Utilisateur::findAll()], 'Gestion des comptes');
    }

    /** POST /admin/utilisateurs/{id}/desactiver */
    public function desactiver(int $id): void
    {
        $this->changerActif($id, false);
    }

    /** POST /admin/utilisateurs/{id}/activer */
    public function activer(int $id): void
    {
        $this->changerActif($id, true);
    }

    private function changerActif(int $id, bool $actif): void
    {
        $user = Utilisateur::findById($id) ?? $this->abort(404, 'Ce compte n\'existe pas.');

        // Un admin qui se désactive perdrait aussitôt l'accès (et il pourrait ne plus rester d'admin).
        if ($user->getId() === Auth::id()) {
            Session::flash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            $this->redirect('/admin/utilisateurs');
        }

        $user->setActif($actif);
        Session::flash('success', 'Compte de ' . $user->getNom() . ($actif ? ' réactivé.' : ' désactivé : la personne ne peut plus se connecter.'));
        $this->redirect('/admin/utilisateurs');
    }
}
