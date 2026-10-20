<?php
declare(strict_types=1);

/**
 * Demande de réservation d'un passager sur un trajet.
 * PROPRIÉTAIRE : Étudiant B (A y ajoute ses méthodes après accord).
 * Consignes : docs/consignes/etudiant-B.md
 */
class Reservation extends Model
{
    public const STATUT_EN_ATTENTE = 'En attente';
    public const STATUT_CONFIRMEE  = 'Confirmée';
    public const STATUT_REFUSEE    = 'Refusée';

    private int $id;
    private string $statut;
    private string $dateReservation;
    private int $idTrajet;
    private int $idPassager;

    // TODO (B) : hydratation + getters
    // TODO (B) : créer une demande, lister les réservations d'un passager, vérifier un doublon…
    // TODO (A) : lister les demandes d'un trajet (avec le nom du passager), changer le statut
}
