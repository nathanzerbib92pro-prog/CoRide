<?php
declare(strict_types=1);

/**
 * MODULE B — Passager. PROPRIÉTAIRE : Étudiant B.
 * Consignes détaillées : docs/consignes/etudiant-B.md
 */
final class ReservationController extends Controller
{
    /** GET /trajets?depart=…&arrivee=…&date=… */
    public function recherche(): void
    {
        // Nettoyage minimal
    $depart  = trim($depart);
    $arrivee = trim($arrivee);
    $date    = trim($date);

    // Requête sécurisée
    $sql = "
        SELECT 
            t.id,
            t.depart,
            t.arrivee,
            t.date_trajet,
            t.heure,
            t.prix,
            t.places,
            u.nom AS conducteur
        FROM trajets t
        JOIN utilisateurs u ON u.id = t.conducteur_id
        WHERE t.depart LIKE :depart
          AND t.arrivee LIKE :arrivee
          AND t.date_trajet = :date
          AND t.places > 0
        ORDER BY t.date_trajet, t.heure
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':depart'  => "%$depart%",
        ':arrivee' => "%$arrivee%",
        ':date'    => $date
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /** POST /trajets/{id}/reserver */
    public function reserver(int $id): void
    {
        / 1. Vérifier si le trajet existe et s'il reste des places
    $sqlCheck = "SELECT places FROM trajets WHERE id = :id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':id' => $trajetId]);
    $trajet = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$trajet) {
        return ['success' => false, 'message' => "Trajet introuvable."];
    }

    if ($trajet['places'] <= 0) {
        return ['success' => false, 'message' => "Plus aucune place disponible."];
    }

    // 2. Insérer la réservation
    $sqlInsert = "
        INSERT INTO reservations (trajet_id, utilisateur_id, date_reservation)
        VALUES (:trajet_id, :utilisateur_id, NOW())
    ";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        ':trajet_id' => $trajetId,
        ':utilisateur_id' => $utilisateurId
    ]);

    // 3. Décrémenter le nombre de places
    $sqlUpdate = "UPDATE trajets SET places = places - 1 WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([':id' => $trajetId]);

    return ['success' => true, 'message' => "Réservation effectuée avec succès !"];
    }

    /** GET /mes-reservations */
    public function mesReservations(): void
    {
        $sql = "
        SELECT 
            r.id AS reservation_id,
            r.statut AS reservation_statut,
            r.date_reservation,

            t.id AS trajet_id,
            t.depart,
            t.arrivee,
            t.date_trajet,
            t.heure,
            t.conducteur_id,
            t.statut AS trajet_statut,   -- ex: 'Ouvert', 'Terminé'

            u.nom AS conducteur_nom
        FROM reservations r
        JOIN trajets t ON r.trajet_id = t.id
        JOIN utilisateurs u ON t.conducteur_id = u.id
        WHERE r.utilisateur_id = :user_id
        ORDER BY t.date_trajet DESC, t.heure DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
