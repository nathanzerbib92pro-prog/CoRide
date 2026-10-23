<?php
/** MODULE C — Avis, réputation et administration (Étudiant C) */

$router->post('/trajets/{id}/cloturer', [AvisController::class, 'cloturer'], ['auth']);
$router->get('/trajets/{id}/avis', [AvisController::class, 'create'], ['auth']);
$router->post('/trajets/{id}/avis', [AvisController::class, 'store'], ['auth']);

$router->get('/admin', [AdminController::class, 'dashboard'], ['auth', 'admin']);
$router->get('/admin/avis', [AdminController::class, 'avis'], ['auth', 'admin']);
$router->post('/admin/avis/{id}/supprimer', [AdminController::class, 'supprimerAvis'], ['auth', 'admin']);
$router->get('/admin/utilisateurs', [AdminController::class, 'utilisateurs'], ['auth', 'admin']);
$router->post('/admin/utilisateurs/{id}/desactiver', [AdminController::class, 'desactiver'], ['auth', 'admin']);
$router->post('/admin/utilisateurs/{id}/activer', [AdminController::class, 'activer'], ['auth', 'admin']);
