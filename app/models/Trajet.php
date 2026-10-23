<?php
declare(strict_types=1);

/**
 * Trajet publié par un conducteur.
 * PROPRIÉTAIRE : Étudiant A (B et C y ajoutent leurs méthodes via des commits dédiés, après accord).
 * Consignes : docs/consignes/etudiant-A.md
 */
class Trajet extends Model
{
    // Statuts : utilisez ces constantes partout, jamais les chaînes en dur.
    public const STATUT_OUVERT  = 'Ouvert';
    public const STATUT_COMPLET = 'Complet';
    public const STATUT_TERMINE = 'Terminé';

    private int $id;
    private string $origine;
    private string $destination;
    private string $dateHeure;
    private ?float $prix;
    private int $placesTotales;
    private int $placesRestantes;
    private string $statut;
    private int $idConducteur;

    // Ajouté par C (clôture et avis) en attendant le module A — cf. issue #6.
    /** Construit un objet Trajet à partir d'une ligne SQL. */
    public static function fromRow(array $row): self
    {
        $t = new self();
        $t->id              = (int) $row['id'];
        $t->origine         = $row['origine'];
        $t->destination     = $row['destination'];
        $t->dateHeure       = $row['date_heure'];
        $t->prix            = $row['prix'] === null ? null : (float) $row['prix'];
        $t->placesTotales   = (int) $row['places_totales'];
        $t->placesRestantes = (int) $row['places_restantes'];
        $t->statut          = $row['statut'];
        $t->idConducteur    = (int) $row['id_conducteur'];
        return $t;
    }

    public function getId(): int { return $this->id; }
    public function getOrigine(): string { return $this->origine; }
    public function getDestination(): string { return $this->destination; }
    public function getDateHeure(): string { return $this->dateHeure; }
    public function getPrix(): ?float { return $this->prix; }
    public function getPlacesTotales(): int { return $this->placesTotales; }
    public function getPlacesRestantes(): int { return $this->placesRestantes; }
    public function getStatut(): string { return $this->statut; }
    public function getIdConducteur(): int { return $this->idConducteur; }

    public function isTermine(): bool { return $this->statut === self::STATUT_TERMINE; }

    /** Le départ est-il passé ? (sinon on ne peut ni clôturer ni noter) */
    public function isDepartPasse(): bool
    {
        return new DateTime($this->dateHeure) <= new DateTime();
    }

    /** Contrat d'équipe : lecture d'un trajet par son id (utilisé par B et C). */
    public static function findById(int $id): ?self
    {
        $stmt = self::db()->prepare('SELECT * FROM trajets WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    // TODO (A) : méthodes d'accès aux données dont votre module a besoin
    //            (créer, lister les trajets d'un conducteur…)

    // --- Clôture (module C) ------------------------------------------------

    /** Délai après le départ au-delà duquel un trajet est clôturé automatiquement. */
    public const DELAI_CLOTURE_AUTO = '24 HOUR';

    /**
     * Passe ce trajet à « Terminé » (clôture manuelle par le conducteur).
     * Les demandes encore « En attente » sont refusées : le trajet a eu lieu sans elles.
     */
    public function terminer(): void
    {
        $db = self::db();
        $db->beginTransaction();
        $stmt = $db->prepare('UPDATE trajets SET statut = :termine WHERE id = :id');
        $stmt->execute(['termine' => self::STATUT_TERMINE, 'id' => $this->id]);
        $stmt = $db->prepare('UPDATE reservations SET statut = :refusee WHERE id_trajet = :id AND statut = :attente');
        $stmt->execute([
            'refusee' => Reservation::STATUT_REFUSEE,
            'attente' => Reservation::STATUT_EN_ATTENTE,
            'id'      => $this->id,
        ]);
        $db->commit();
        $this->statut = self::STATUT_TERMINE;
    }

    /**
     * Clôture automatique : tous les trajets « Ouvert » ou « Complet » partis depuis plus
     * de DELAI_CLOTURE_AUTO passent à « Terminé ». Renvoie le nombre de trajets clôturés.
     * Appelée par bin/cloturer-trajets.php (tâche planifiée) et à l'ouverture des pages du module C.
     */
    public static function cloturerTrajetsPasses(): int
    {
        $db = self::db();
        $db->beginTransaction();
        $limite = 'NOW() - INTERVAL ' . self::DELAI_CLOTURE_AUTO;
        // Les demandes en attente d'abord, tant qu'on sait encore quels trajets sont concernés.
        $stmt = $db->prepare(
            "UPDATE reservations r JOIN trajets t ON t.id = r.id_trajet
             SET r.statut = :refusee
             WHERE r.statut = :attente AND t.statut IN (:ouvert, :complet) AND t.date_heure <= $limite"
        );
        $stmt->execute([
            'refusee' => Reservation::STATUT_REFUSEE,
            'attente' => Reservation::STATUT_EN_ATTENTE,
            'ouvert'  => self::STATUT_OUVERT,
            'complet' => self::STATUT_COMPLET,
        ]);
        $stmt = $db->prepare(
            "UPDATE trajets SET statut = :termine
             WHERE statut IN (:ouvert, :complet) AND date_heure <= $limite"
        );
        $stmt->execute([
            'termine' => self::STATUT_TERMINE,
            'ouvert'  => self::STATUT_OUVERT,
            'complet' => self::STATUT_COMPLET,
        ]);
        $nb = $stmt->rowCount();
        $db->commit();
        return $nb;
    }

    // TODO (B) : méthode de recherche filtrée (ville de départ, d'arrivée, date) — trajets « Ouvert » uniquement

    /**
     * Méthode imposée par le sujet.
     * Vérifie la disponibilité, décrémente places_restantes,
     * bascule le statut en « Complet » quand il ne reste plus de place.
     * Retourne true si la place a bien été prise, false sinon.
     *
     * Avant de coder : décidez en équipe QUAND elle est appelée
     * (à la demande du passager ? à l'acceptation par le conducteur ?). Voir consignes A et B.
     */
    public function reserverPlace(int $idPassager): bool
    {
        throw new LogicException('Trajet::reserverPlace() à implémenter (Étudiant A).');
    }
}
