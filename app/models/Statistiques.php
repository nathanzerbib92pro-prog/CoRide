<?php
declare(strict_types=1);

/**
 * Statistiques globales du tableau de bord admin.
 * PROPRIÉTAIRE : Étudiant C. Formules détaillées : docs/module-c.md
 */
final class Statistiques extends Model
{
    /** Nombre de trajets réalisés (statut « Terminé »). */
    public static function trajetsRealises(): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM trajets WHERE statut = :termine');
        $stmt->execute(['termine' => Trajet::STATUT_TERMINE]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Taux de remplissage moyen des trajets réalisés, entre 0 et 1 (null s'il n'y en a aucun).
     * Pour chaque trajet terminé : passagers confirmés ÷ places proposées ; puis moyenne des trajets.
     */
    public static function tauxRemplissageMoyen(): ?float
    {
        $stmt = self::db()->prepare(
            'SELECT AVG(occupation.confirmees / occupation.places_totales)
             FROM (
                 SELECT t.places_totales,
                        (SELECT COUNT(*) FROM reservations r
                         WHERE r.id_trajet = t.id AND r.statut = :confirmee) AS confirmees
                 FROM trajets t
                 WHERE t.statut = :termine
             ) AS occupation'
        );
        $stmt->execute(['confirmee' => Reservation::STATUT_CONFIRMEE, 'termine' => Trajet::STATUT_TERMINE]);
        $taux = $stmt->fetchColumn();
        return $taux === null ? null : (float) $taux;
    }

    public static function comptesActifs(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM utilisateurs WHERE actif = 1')->fetchColumn();
    }
}
