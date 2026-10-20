<?php
declare(strict_types=1);

/**
 * Connexion PDO unique partagée par toute l'application (patron Singleton).
 * Usage : $pdo = Database::get();
 */
final class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $c = self::$config;
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $c['host'],
                (int) ($c['port'] ?? 3306),
                $c['name']
            );

            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // les erreurs SQL lèvent des exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,                  // vraies requêtes préparées
            ]);

            // Aligne l'heure MySQL sur celle de PHP (important pour NOW() et la clôture des trajets)
            self::$pdo->exec("SET time_zone = '" . (new DateTime())->format('P') . "'");
        }

        return self::$pdo;
    }

    private function __construct()
    {
    }
}
