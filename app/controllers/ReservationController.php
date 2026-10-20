<?php
declare(strict_types=1);

/**
 * MODULE B — Passager. PROPRIÉTAIRE : Étudiant B.
 * Consignes détaillées : docs/consignes/etudiant-B.md
 */
final class ReservationController extends Controller
{
    /** GET /trajets?depart=…&arrivee=…&date=… */
    public function recherche(): void
    {
        $this->todo('B', 'Rechercher un trajet', 'Liste filtrable par ville de départ, ville d\'arrivée et date (trajets « Ouvert » uniquement).');
    }

    /** POST /trajets/{id}/reserver */
    public function reserver(int $id): void
    {
        $this->todo('B', 'Réserver une place', "Envoyer une demande sur le trajet n°$id en respectant les règles métier.");
    }

    /** GET /mes-reservations */
    public function mesReservations(): void
    {
        $this->todo('B', 'Mes réservations', 'État de chaque demande : En attente, Confirmée, Refusée.');
    }
}
