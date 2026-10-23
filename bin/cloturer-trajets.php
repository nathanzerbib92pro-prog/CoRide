<?php
/**
 * Clôture automatique des trajets passés (module C).
 * Tâche planifiée AlwaysData (Avancé › Tâches planifiées), par exemple toutes les heures :
 *   php $HOME/coride/bin/cloturer-trajets.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('ROOT', dirname(__DIR__));
require ROOT . '/app/bootstrap.php';

$nb = Trajet::cloturerTrajetsPasses();
echo date('Y-m-d H:i:s') . " : $nb trajet(s) clôturé(s)." . PHP_EOL;
