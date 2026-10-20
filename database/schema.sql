-- ==========================================================================
-- CoRide — schéma de la base (MySQL 8 / MariaDB 10.5+)
-- Import : phpMyAdmin > Importer, ou  mysql -u USER -p NOM_BDD < schema.sql
-- ==========================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS avis, reservations, trajets, utilisateurs;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE utilisateurs (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom         VARCHAR(100) NOT NULL,
    email       VARCHAR(190) NOT NULL,
    password    VARCHAR(255) NOT NULL,                 -- hash password_hash()
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    actif       TINYINT(1) NOT NULL DEFAULT 1,         -- gestion des comptes par l'admin
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_utilisateurs_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trajets (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    origine           VARCHAR(120) NOT NULL,
    destination       VARCHAR(120) NOT NULL,
    date_heure        DATETIME NOT NULL,
    prix              DECIMAL(6,2) NULL,               -- prix indicatif par passager (formulaire du sujet)
    places_totales    TINYINT UNSIGNED NOT NULL,
    places_restantes  TINYINT UNSIGNED NOT NULL,       -- UNSIGNED : ne peut jamais passer sous 0
    statut            ENUM('Ouvert','Complet','Terminé') NOT NULL DEFAULT 'Ouvert',
    id_conducteur     INT UNSIGNED NOT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trajets_recherche (statut, origine, destination, date_heure),
    KEY idx_trajets_conducteur (id_conducteur),
    CONSTRAINT fk_trajets_conducteur FOREIGN KEY (id_conducteur) REFERENCES utilisateurs (id),
    CONSTRAINT chk_trajets_places CHECK (places_totales BETWEEN 1 AND 8 AND places_restantes <= places_totales),
    CONSTRAINT chk_trajets_prix CHECK (prix IS NULL OR prix >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reservations (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    statut            ENUM('En attente','Confirmée','Refusée') NOT NULL DEFAULT 'En attente',
    date_reservation  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_trajet         INT UNSIGNED NOT NULL,
    id_passager       INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reservation_trajet_passager (id_trajet, id_passager),   -- règle : pas deux fois le même trajet
    KEY idx_reservations_passager (id_passager),
    CONSTRAINT fk_reservations_trajet   FOREIGN KEY (id_trajet)   REFERENCES trajets (id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_passager FOREIGN KEY (id_passager) REFERENCES utilisateurs (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE avis (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    note         TINYINT UNSIGNED NOT NULL,
    commentaire  VARCHAR(1000) NULL,
    date         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_trajet    INT UNSIGNED NOT NULL,
    id_auteur    INT UNSIGNED NOT NULL,
    id_cible     INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_avis_trajet_auteur_cible (id_trajet, id_auteur, id_cible), -- règle : une seule note par personne et par trajet
    KEY idx_avis_cible (id_cible),
    CONSTRAINT fk_avis_trajet FOREIGN KEY (id_trajet) REFERENCES trajets (id) ON DELETE CASCADE,
    CONSTRAINT fk_avis_auteur FOREIGN KEY (id_auteur) REFERENCES utilisateurs (id),
    CONSTRAINT fk_avis_cible  FOREIGN KEY (id_cible)  REFERENCES utilisateurs (id),
    CONSTRAINT chk_avis_note CHECK (note BETWEEN 1 AND 5),
    CONSTRAINT chk_avis_pas_soi_meme CHECK (id_auteur <> id_cible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
