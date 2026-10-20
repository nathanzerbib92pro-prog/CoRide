-- ==========================================================================
-- Jeu de données de démonstration (à importer APRÈS schema.sql)
-- Mot de passe de TOUS les comptes : CoRide2026!
-- ==========================================================================
SET NAMES utf8mb4;

INSERT INTO utilisateurs (id, nom, email, password, role) VALUES
(1, 'Admin CoRide', 'admin@coride.test', '$2y$10$TRysFupXdV/.2hcCtFaxa.r..3K4mQ7Nhg1sYPqj8HJh7ie7At6pK', 'admin'),
(2, 'Lucas Martin', 'lucas@coride.test', '$2y$10$TRysFupXdV/.2hcCtFaxa.r..3K4mQ7Nhg1sYPqj8HJh7ie7At6pK', 'user'),
(3, 'Inès Haddad',  'ines@coride.test',  '$2y$10$TRysFupXdV/.2hcCtFaxa.r..3K4mQ7Nhg1sYPqj8HJh7ie7At6pK', 'user'),
(4, 'Mehdi Benali', 'mehdi@coride.test', '$2y$10$TRysFupXdV/.2hcCtFaxa.r..3K4mQ7Nhg1sYPqj8HJh7ie7At6pK', 'user'),
(5, 'Sarah Cohen',  'sarah@coride.test', '$2y$10$TRysFupXdV/.2hcCtFaxa.r..3K4mQ7Nhg1sYPqj8HJh7ie7At6pK', 'user'),
(6, 'Tom Leroy',    'tom@coride.test',   '$2y$10$TRysFupXdV/.2hcCtFaxa.r..3K4mQ7Nhg1sYPqj8HJh7ie7At6pK', 'user');

-- 4 trajets dans les 3 statuts
INSERT INTO trajets (id, origine, destination, date_heure, prix, places_totales, places_restantes, statut, id_conducteur) VALUES
(1, 'Paris',  'Lille',   '2026-10-02 18:00:00', 12.00, 3, 1, 'Terminé', 2),  -- terminé : notation ouverte
(2, 'Paris',  'Rouen',   '2026-11-27 17:30:00',  8.00, 2, 0, 'Complet', 3),  -- complet
(3, 'Lille',  'Paris',   '2026-11-29 15:00:00', 12.00, 4, 4, 'Ouvert',  2),  -- ouvert, 1 demande en attente
(4, 'Paris',  'Orléans', '2026-12-04 08:00:00',  7.50, 3, 3, 'Ouvert',  4);  -- ouvert, aucune demande

-- 6 réservations dans les 3 statuts
INSERT INTO reservations (id, statut, date_reservation, id_trajet, id_passager) VALUES
(1, 'Confirmée',  '2026-09-28 10:12:00', 1, 3),
(2, 'Confirmée',  '2026-09-29 19:40:00', 1, 4),
(3, 'Confirmée',  '2026-10-05 08:30:00', 2, 5),
(4, 'Confirmée',  '2026-10-06 12:05:00', 2, 6),
(5, 'En attente', '2026-10-08 21:15:00', 3, 5),
(6, 'Refusée',    '2026-10-07 09:00:00', 3, 6);

-- 4 avis sur le trajet terminé (conducteur <-> passagers)
INSERT INTO avis (id, note, commentaire, date, id_trajet, id_auteur, id_cible) VALUES
(1, 5, 'Ponctuel, conduite tranquille, je recommande.',      '2026-10-03 09:00:00', 1, 3, 2),
(2, 4, 'Bon trajet, un peu de retard au départ.',            '2026-10-03 11:20:00', 1, 4, 2),
(3, 5, 'Passagère au rendez-vous et très sympa.',             '2026-10-03 12:00:00', 1, 2, 3),
(4, 3, 'Arrivé 15 minutes après l''heure convenue.',          '2026-10-04 08:45:00', 1, 2, 4);

ALTER TABLE utilisateurs AUTO_INCREMENT = 7;
ALTER TABLE trajets AUTO_INCREMENT = 5;
ALTER TABLE reservations AUTO_INCREMENT = 7;
ALTER TABLE avis AUTO_INCREMENT = 5;
