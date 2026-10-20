<?php
declare(strict_types=1);

/**
 * Classe mère des modèles : accès à la connexion PDO partagée.
 */
abstract class Model
{
    protected static function db(): PDO
    {
        return Database::get();
    }
}
