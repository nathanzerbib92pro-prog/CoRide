<?php
/** MODULE C — Avis, réputation et administration (Étudiant C) */

$router->post('/trajets/{id}/cloturer', [AvisController::class, 'cloturer'], ['auth']);
$router->get('/trajets/{id}/avis', [AvisController::class, 'create'], ['auth']);
$router->post('/trajets/{id}/avis', [AvisController::class, 'store'], ['auth']);

$router->get('/admin', [AdminController::class, 'dashboard'], ['auth', 'admin']);
$router->get('/admin/avis', [AdminController::class, 'avis'], ['auth', 'admin']);
$router->post('/admin/avis/{id}/supprimer', [AdminController::class, 'supprimerAvis'], ['auth', 'admin']);
$router->get('/admin/utilisateurs', [AdminController::class, 'utilisateurs'], ['auth', 'admin']);
// TODO (C) : ajouter ici les routes d'action sur les comptes
