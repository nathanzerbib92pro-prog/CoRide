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
            // 1) Profil
    $stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profil) {
        http_response_code(404);
        echo "Profil inexistant.";
        return;
    }

    // 2) Trajets réalisés (conducteur uniquement)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM trajets
        WHERE conducteur_id = :id
          AND statut = 'Terminé'
    ");
    $stmt->execute([':id' => $id]);
    $trajetsRealises = (int) $stmt->fetchColumn();

    // 3) Note moyenne
    $note = Avis::noteMoyenne($id); // peut être null

    if ($note === null) {
        $noteAffichee = "Pas encore noté";
    } else {
        $noteAffichee = number_format($note, 1, ',', ' ');
    }

    // 4) Envoi à la vue
    include 'views/profil_public.php';
    }
}
