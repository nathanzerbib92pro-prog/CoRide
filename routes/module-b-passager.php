<?php
/** MODULE B — Passager (Étudiant B) */

$router->get('/trajets', [ReservationController::class, 'recherche']);
$router->post('/trajets/{id}/reserver', [ReservationController::class, 'reserver'], ['auth']);
$router->get('/mes-reservations', [ReservationController::class, 'mesReservations'], ['auth']);
$router->get('/profil/{id}', [ProfilController::class, 'show'], ['auth']);
