<?php
declare(strict_types=1);

/**
 * Authentification partagée par les trois modules.
 *   Auth::check()   → un utilisateur est-il connecté ?
 *   Auth::id()      → id de l'utilisateur connecté (à utiliser à la place de tout id venant d'un formulaire)
 *   Auth::user()    → objet Utilisateur
 *   Auth::isAdmin() → rôle admin ?
 */
final class Auth
{
    private static ?Utilisateur $cache = null;

    public static function login(Utilisateur $user): void
    {
        Session::regenerate();
        Session::set('user_id', $user->getId());
        self::$cache = $user;
    }

    public static function logout(): void
    {
        self::$cache = null;
        Session::destroy();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()?->getId();
    }

    public static function user(): ?Utilisateur
    {
        if (self::$cache === null) {
            $id = Session::get('user_id');
            if ($id === null) {
                return null;
            }
            self::$cache = Utilisateur::findById((int) $id);
            // Compte supprimé ou désactivé entre-temps : on déconnecte.
            if (self::$cache === null || !self::$cache->isActif()) {
                self::$cache = null;
                Session::remove('user_id');
            }
        }
        return self::$cache;
    }

    public static function isAdmin(): bool
    {
        return self::user()?->isAdmin() ?? false;
    }
}
