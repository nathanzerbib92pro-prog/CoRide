<?php
declare(strict_types=1);

/**
 * MODULE C — Avis & réputation. PROPRIÉTAIRE : Étudiant C.
 * Consignes détaillées : docs/consignes/etudiant-C.md
 */
final class AvisController extends Controller
{
    /** POST /trajets/{id}/cloturer */
    public function cloturer(int $id): void
    {
        $this->todo('C', 'Clôturer un trajet', "Passer le trajet n°$id au statut « Terminé » (débloque la notation).");
    }

    /** GET /trajets/{id}/avis */
    public function create(int $id): void
    {
        $this->todo('C', 'Laisser un avis', 'Note de 1 à 5 + commentaire, uniquement sur un trajet terminé.');
    }

    /** POST /trajets/{id}/avis */
    public function store(int $id): void
    {
        $this->todo('C', 'Laisser un avis', 'Contrôler les règles métier puis enregistrer l\'avis.');
    }
}
