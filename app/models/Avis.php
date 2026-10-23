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

    /** Construit un objet Avis à partir d'une ligne SQL. */
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

    /** Cet auteur a-t-il déjà noté cette personne sur ce trajet ? */
    public static function dejaNote(int $idTrajet, int $idAuteur, int $idCible): bool
    {
        $stmt = self::db()->prepare(
            'SELECT 1 FROM avis
             WHERE id_trajet = :trajet AND id_auteur = :auteur AND id_cible = :cible'
        );
        $stmt->execute(['trajet' => $idTrajet, 'auteur' => $idAuteur, 'cible' => $idCible]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Personnes que $idAuteur peut noter sur ce trajet, déjà notées comprises :
     * [['id' => …, 'nom' => …, 'role' => 'conducteur'|'passager', 'deja_note' => bool], …]
     * Conducteur ↔ chaque passager « Confirmée » (décision d'équipe n°3).
     * Tableau vide si l'auteur n'a pas participé au trajet.
     */
    public static function cibles(Trajet $trajet, int $idAuteur): array
    {
        $stmt = self::db()->prepare(
            'SELECT u.id, u.nom FROM reservations r
             JOIN utilisateurs u ON u.id = r.id_passager
             WHERE r.id_trajet = :trajet AND r.statut = :confirmee
             ORDER BY u.nom'
        );
        $stmt->execute(['trajet' => $trajet->getId(), 'confirmee' => Reservation::STATUT_CONFIRMEE]);
        $passagers = $stmt->fetchAll();

        if ($idAuteur === $trajet->getIdConducteur()) {
            $cibles = array_map(fn(array $p) => $p + ['role' => 'passager'], $passagers);
        } elseif (in_array($idAuteur, array_map(fn(array $p) => (int) $p['id'], $passagers), true)) {
            $conducteur = Utilisateur::findById($trajet->getIdConducteur());
            $cibles = [['id' => $conducteur->getId(), 'nom' => $conducteur->getNom(), 'role' => 'conducteur']];
        } else {
            return [];
        }

        foreach ($cibles as &$c) {
            $c['id'] = (int) $c['id'];
            $c['deja_note'] = self::dejaNote($trajet->getId(), $idAuteur, $c['id']);
        }
        return $cibles;
    }

    /** Tous les avis, du plus récent au plus ancien, avec les noms et le trajet (modération). */
    public static function listerPourModeration(): array
    {
        return self::db()->query(
            'SELECT a.id, a.note, a.commentaire, a.date,
                    auteur.nom AS auteur, cible.nom AS cible,
                    t.id AS id_trajet, t.origine, t.destination
             FROM avis a
             JOIN utilisateurs auteur ON auteur.id = a.id_auteur
             JOIN utilisateurs cible  ON cible.id  = a.id_cible
             JOIN trajets t           ON t.id      = a.id_trajet
             ORDER BY a.date DESC, a.id DESC'
        )->fetchAll();
    }

    /** Supprime un avis. Renvoie false s'il n'existait pas. */
    public static function supprimer(int $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM avis WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() === 1;
    }

    public static function compter(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM avis')->fetchColumn();
    }
}
