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

    public static function fromRow(array $row): self
    {
        $a = new self();
        $a->id          = (int) $row['id'];
        $a->note        = (int) $row['note'];
        $a->commentaire = $row['commentaire'];
        $a->date        = $row['date'];
        $a->idTrajet    = (int) $row['id_trajet'];
        $a->idAuteur    = (int) $row['id_auteur'];
        $a->idCible     = (int) $row['id_cible'];
        return $a;
    }
    // TODO (C) : créer un avis, vérifier qu'un auteur n'a pas déjà noté cette personne sur ce trajet,
    //            lister les avis reçus, supprimer (modération)
    /** Construit un objet Avis à partir d'une ligne SQL. */
    public function getId(): int
    {
        return $this->id;
    }
    public function getNote(): int
    {
        return $this->note;
    }
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getIdTrajet(): int
    {
        return $this->idTrajet;
    }
    public function getIdAuteur(): int
    {
        return $this->idAuteur;
    }
    public function getIdCible(): int
    {
        return $this->idCible;
    }

    /**
     * Méthode statique imposée par le sujet.
     * Moyenne des notes reçues par un utilisateur, ou null s'il n'a encore aucun avis.
     *
     * ⚠ A et B l'utilisent dès le début : gardez cette signature, même si le corps n'est pas fini.
     */
    public static function noteMoyenne(int $idUtilisateur): ?float
    {
        $stmt = self::db()->prepare('SELECT AVG(note) FROM avis WHERE id_cible = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $moyenne = $stmt->fetchColumn();

        if ($moyenne === null) {
            return null;
        }

        return (float) $moyenne;
    }

    /** Enregistre un nouvel avis et renvoie son id. */
    public static function creer(int $idTrajet, int $idAuteur, int $idCible, int $note, ?string $commentaire): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO avis (note, commentaire, id_trajet, id_auteur, id_cible)
             VALUES (:note, :commentaire, :trajet, :auteur, :cible)'
        );
        $stmt->execute([
            'note'        => $note,
            'commentaire' => $commentaire,
            'trajet'      => $idTrajet,
            'auteur'      => $idAuteur,
            'cible'       => $idCible,
        ]);
        return (int) self::db()->lastInsertId();
    }
}
