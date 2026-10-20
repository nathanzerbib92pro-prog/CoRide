<?php
declare(strict_types=1);

/**
 * Protection CSRF : chaque formulaire POST contient un jeton vérifié automatiquement par le Router.
 * Dans une vue : <?= Csrf::field() ?>
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function isValid(?string $token): bool
    {
        return is_string($token) && hash_equals(self::token(), $token);
    }
}
