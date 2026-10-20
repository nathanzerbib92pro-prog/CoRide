<?php
declare(strict_types=1);

/** Échappe une valeur pour l'afficher dans du HTML (protection XSS). À utiliser dans TOUTES les vues. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Préfixe d'URL si l'application est dans un sous-dossier ('' si à la racine). */
function base_path(): string
{
    static $base = null;
    if ($base === null) {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $dir = preg_replace('#/public$#', '', rtrim($dir, '/'));
        $base = $dir === '/' ? '' : $dir;
    }
    return $base;
}

/** URL interne : url('/trajets') */
function url(string $path = '/'): string
{
    return base_path() . '/' . ltrim($path, '/');
}

/** Fichier statique de public/assets : asset('css/app.css') */
function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

/** Lien de navigation actif ? */
function is_active(string $path): bool
{
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    return rtrim($current, '/') === rtrim(url($path), '/');
}

/** Date lisible en français : « ven. 20 nov. à 18h30 » */
function date_fr(string $datetime): string
{
    $d = new DateTime($datetime);
    $jours = ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'];
    $mois  = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
    return sprintf(
        '%s %d %s à %sh%s',
        $jours[(int) $d->format('w')],
        (int) $d->format('j'),
        $mois[(int) $d->format('n') - 1],
        $d->format('H'),
        $d->format('i')
    );
}
