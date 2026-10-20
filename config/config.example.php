<?php
/**
 * Copiez ce fichier en config/config.php (ignoré par Git) et complétez-le.
 *
 * Sur AlwaysData :
 *   host => 'mysql-<compte>.alwaysdata.net'
 *   name => '<compte>_coride'   (les noms de BDD sont préfixés par le compte)
 *   user => '<compte>' ou un utilisateur créé dans Bases de données > Utilisateurs
 */
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'coride',
        'user' => 'root',
        'pass' => '',
    ],
    'app' => [
        'name'     => 'CoRide',
        'debug'    => true,          // false en production !
        'timezone' => 'Europe/Paris',
    ],
];
