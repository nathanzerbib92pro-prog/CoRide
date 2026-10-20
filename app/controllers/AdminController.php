<?php
declare(strict_types=1);

/**
 * MODULE C — Espace admin. PROPRIÉTAIRE : Étudiant C.
 * Le middleware 'admin' (routes/module-c-avis.php) bloque déjà les non-admins.
 */
final class AdminController extends Controller
{
    /** GET /admin */
    public function dashboard(): void
    {
        $this->todo('C', 'Tableau de bord admin', 'Statistiques globales : trajets réalisés, taux de remplissage moyen.');
    }

    /** GET /admin/avis */
    public function avis(): void
    {
        $this->todo('C', 'Modération des avis', 'Lister les avis et supprimer les commentaires abusifs.');
    }

    /** POST /admin/avis/{id}/supprimer */
    public function supprimerAvis(int $id): void
    {
        $this->todo('C', 'Supprimer un avis', "Suppression de l'avis n°$id.");
    }

    /** GET /admin/utilisateurs */
    public function utilisateurs(): void
    {
        $this->todo('C', 'Gestion des comptes', 'Lister les comptes et permettre de les gérer.');
    }
}
