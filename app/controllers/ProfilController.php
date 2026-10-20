<?php
declare(strict_types=1);

/**
 * MODULE B — Profil public. PROPRIÉTAIRE : Étudiant B.
 */
final class ProfilController extends Controller
{
    /** GET /profil/{id} */
    public function show(int $id): void
    {
        $this->todo('B', 'Profil public', 'Nom, note moyenne et nombre de trajets réalisés.');
    }
}
