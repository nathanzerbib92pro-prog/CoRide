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

    // TODO (A) : hydratation depuis une ligne SQL — inspirez-vous de Utilisateur::fromRow()
    class Trajet
{
    private function __construct(
        private int $id,
        private string $depart,
        private string $arrivee,
        private string $dateDepart,
        private float $prix,
        private int $placesRestantes,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            $row['depart'],
            $row['arrivee'],
            $row['date_depart'],
            (float) $row['prix'],
            (int) $row['places_restantes'], // string -> int
        );
    }
    // TODO (A) : getters
        public function getId(): int { return $this->id; }
    public function getDepart(): string { return $this->depart; }
    public function getArrivee(): string { return $this->arrivee; }
    public function getDateDepart(): string { return $this->dateDepart; }
    public function getPrix(): float { return $this->prix; }
    public function getPlacesRestantes(): int { return $this->placesRestantes; }

    // TODO (A) : méthodes d'accès aux données dont votre module a besoin
    //            (créer, trouver par id, lister les trajets d'un conducteur…)
        public static function findById(PDO $pdo, int $id): ?self
    {
        $stmt = $pdo->prepare('SELECT * FROM trajet WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : self::fromRow($row);
    }
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
