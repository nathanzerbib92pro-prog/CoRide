<?php
declare(strict_types=1);

$configFile = ROOT . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('config/config.php manquant : copiez config/config.example.php et renseignez vos identifiants.');
}
$config = require $configFile;

date_default_timezone_set($config['app']['timezone'] ?? 'Europe/Paris');
error_reporting(E_ALL);
ini_set('display_errors', !empty($config['app']['debug']) ? '1' : '0');

// Autoload maison : une classe = un fichier du même nom dans core/, models/ ou controllers/
spl_autoload_register(function (string $class): void {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = ROOT . "/app/$dir/$class.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

require ROOT . '/app/core/helpers.php';

Database::configure($config['db']);
Session::start();

// En production, on n'affiche jamais le détail d'une exception à l'utilisateur.
set_exception_handler(function (Throwable $e) use ($config): void {
    http_response_code(500);
    error_log((string) $e);
    if (!empty($config['app']['debug'])) {
        echo '<pre>' . e((string) $e) . '</pre>';
        return;
    }
    echo 'Une erreur est survenue. Réessayez plus tard.';
});
