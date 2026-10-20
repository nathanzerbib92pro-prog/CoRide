<?php
declare(strict_types=1);

/**
 * MODULE A — Conducteur. PROPRIÉTAIRE : Étudiant A.
 * Consignes détaillées : docs/consignes/etudiant-A.md
 * Remplacez chaque appel à $this->todo() par votre implémentation.
 */
final class TrajetController extends Controller
{
    /** GET /trajets/publier */
    public function create(): void
    {
        $this->todo('A', 'Publier un trajet', 'Formulaire : origine, destination, date et heure, nombre de places, prix indicatif.');
    }

    /** POST /trajets/publier */
    public function store(): void
    {
        $this->todo('A', 'Publier un trajet', 'Valider les champs, enregistrer le trajet, rediriger vers « Mes trajets ».');
    }

    /** GET /mes-trajets */
    public function mesTrajets(): void
    {
        $this->todo('A', 'Mes trajets', 'Historique des trajets publiés avec leur statut (Ouvert, Complet, Terminé).');
    }

    /** GET /mes-trajets/{id} */
    public function demandes(int $id): void
    {
        $this->todo('A', 'Demandes de réservation', "Passagers ayant demandé une place sur le trajet n°$id, avec leur note moyenne.");
    }

    /** POST /reservations/{id}/accepter */
    public function accepterReservation(int $id): void
    {
        $this->todo('A', 'Accepter une réservation', 'Accepter la demande et décrémenter les places restantes.');
    }

    /** POST /reservations/{id}/refuser */
    public function refuserReservation(int $id): void
    {
        $this->todo('A', 'Refuser une réservation', 'Passer la demande au statut « Refusée ».');
    }

    /** GET /conducteur/statistiques */
    public function statistiques(): void
    {
        $this->todo('A', 'Mes statistiques', 'Nombre de trajets réalisés, passager le plus fréquent.');
    }
}
