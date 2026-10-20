<?php
/** Routes communes — à modifier uniquement en accord avec l'équipe. */

$router->get('/', [HomeController::class, 'index']);

$router->get('/connexion', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/connexion', [AuthController::class, 'login'], ['guest']);
$router->get('/inscription', [AuthController::class, 'showRegister'], ['guest']);
$router->post('/inscription', [AuthController::class, 'register'], ['guest']);
$router->post('/deconnexion', [AuthController::class, 'logout'], ['auth']);
