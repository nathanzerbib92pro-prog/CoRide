<?php
/** MODULE A — Conducteur (Étudiant A) */

$router->get('/trajets/publier', [TrajetController::class, 'create'], ['auth']);
$router->post('/trajets/publier', [TrajetController::class, 'store'], ['auth']);
$router->get('/mes-trajets', [TrajetController::class, 'mesTrajets'], ['auth']);
$router->get('/mes-trajets/{id}', [TrajetController::class, 'demandes'], ['auth']);
$router->post('/reservations/{id}/accepter', [TrajetController::class, 'accepterReservation'], ['auth']);
$router->post('/reservations/{id}/refuser', [TrajetController::class, 'refuserReservation'], ['auth']);
$router->get('/conducteur/statistiques', [TrajetController::class, 'statistiques'], ['auth']);
