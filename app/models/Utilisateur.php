<?php
declare(strict_types=1);

/**
 * Utilisateur commun aux trois modules (SOCLE — modifications à valider en équipe).
 * Un même utilisateur peut être conducteur sur un trajet et passager sur un autre :
 * le « rôle » ne concerne que l'administration (user | admin).
 */
class Utilisateur extends Model
{
    public const ROLE_USER  = 'user';
    public const ROLE_ADMIN = 'admin';

    private int $id;
    private string $nom;
    private string $email;
    private string $password; // toujours le HASH, jamais le mot de passe en clair
    private string $role;
    private bool $actif;
    private string $createdAt;

    /** Construit un objet à partir d'une ligne SQL (tableau associatif). */
    public static function fromRow(array $row): self
    {
        $u = new self();
        $u->id        = (int) $row['id'];
        $u->nom       = $row['nom'];
        $u->email     = $row['email'];
        $u->password  = $row['password'];
        $u->role      = $row['role'];
        $u->actif     = (bool) $row['actif'];
        $u->createdAt = $row['created_at'];
        return $u;
    }

    public static function findById(int $id): ?self
    {
        $stmt = self::db()->prepare('SELECT * FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    /** Tous les comptes, par nom (gestion des comptes, module C). */
    public static function findAll(): array
    {
        $rows = self::db()->query('SELECT * FROM utilisateurs ORDER BY nom')->fetchAll();
        return array_map([self::class, 'fromRow'], $rows);
    }

    /**
     * Active ou désactive le compte (module C). On ne supprime pas : les trajets,
     * réservations et avis du compte y restent liés par clés étrangères.
     */
    public function setActif(bool $actif): void
    {
        $stmt = self::db()->prepare('UPDATE utilisateurs SET actif = :actif WHERE id = :id');
        $stmt->execute(['actif' => (int) $actif, 'id' => $this->id]);
        $this->actif = $actif;
    }

    public static function findByEmail(string $email): ?self
    {
        $stmt = self::db()->prepare('SELECT * FROM utilisateurs WHERE email = :email');
        $stmt->execute(['email' => mb_strtolower($email)]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function emailExists(string $email): bool
    {
        $stmt = self::db()->prepare('SELECT 1 FROM utilisateurs WHERE email = :email');
        $stmt->execute(['email' => mb_strtolower($email)]);
        return (bool) $stmt->fetchColumn();
    }

    /** Crée un compte. Le mot de passe est haché ici avec password_hash(). */
    public static function create(string $nom, string $email, string $plainPassword): self
    {
        $stmt = self::db()->prepare(
            'INSERT INTO utilisateurs (nom, email, password) VALUES (:nom, :email, :password)'
        );
        $stmt->execute([
            'nom'      => $nom,
            'email'    => mb_strtolower($email),
            'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
        ]);
        return self::findById((int) self::db()->lastInsertId());
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    /** Si l'algorithme par défaut de PHP évolue, on met le hash à jour à la connexion. */
    public function rehashIfNeeded(string $plainPassword): void
    {
        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->password = password_hash($plainPassword, PASSWORD_DEFAULT);
            $stmt = self::db()->prepare('UPDATE utilisateurs SET password = :p WHERE id = :id');
            $stmt->execute(['p' => $this->password, 'id' => $this->id]);
        }
    }

    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function isActif(): bool { return $this->actif; }
    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }

    /** Initiales pour les avatars : « Inès Haddad » → « IH » */
    public function getInitiales(): string
    {
        $parts = preg_split('/\s+/', trim($this->nom));
        $ini = mb_substr($parts[0], 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '');
        return mb_strtoupper($ini);
    }
}
