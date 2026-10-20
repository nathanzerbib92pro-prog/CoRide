<?php
/**
 * Front controller : TOUTES les requêtes passent par ici (cf. .htaccess).
 */
declare(strict_types=1);

// Serveur intégré de PHP (php -S) : servir directement les fichiers statiques.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

define('ROOT', dirname(__DIR__));
require ROOT . '/app/bootstrap.php';

$router = new Router();

// Chaque module déclare ses routes dans son propre fichier (moins de conflits Git).
foreach (glob(ROOT . '/routes/*.php') as $routeFile) {
    require $routeFile;
}

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
