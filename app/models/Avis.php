<?php
declare(strict_types=1);

/**
 * Avis laissé après un trajet terminé (note 1 à 5 + commentaire).
 * PROPRIÉTAIRE : Étudiant C.
 * Consignes : docs/consignes/etudiant-C.md
 */
class Avis extends Model
{
    public const NOTE_MIN = 1;
    public const NOTE_MAX = 5;

    private int $id;
    private int $note;
    private ?string $commentaire;
    private string $date;
    private int $idTrajet;
    private int $idAuteur;
    private int $idCible;

    // TODO (C) : hydratation + getters
    // TODO (C) : créer un avis, vérifier qu'un auteur n'a pas déjà noté cette personne sur ce trajet,
    //            lister les avis reçus, supprimer (modération)

    /**
     * Méthode statique imposée par le sujet.
     * Moyenne des notes reçues par un utilisateur, ou null s'il n'a encore aucun avis.
     *
     * ⚠ A et B l'utilisent dès le début : gardez cette signature, même si le corps n'est pas fini.
     */
    public static function noteMoyenne(int $idUtilisateur): ?float
    {
        return null; // TODO (C) : remplacer ce bouchon par le vrai calcul
    }
}
