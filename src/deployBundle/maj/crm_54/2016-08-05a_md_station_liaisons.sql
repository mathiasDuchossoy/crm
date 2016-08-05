CREATE TABLE grande_ville_traduction (
  id              INT UNSIGNED AUTO_INCREMENT NOT NULL,
  grande_ville_id INT UNSIGNED DEFAULT NULL,
  langue_id       INT UNSIGNED DEFAULT NULL,
  libelle         VARCHAR(255)                NOT NULL,
  INDEX IDX_FC64968BAFDEF94D (grande_ville_id),
  INDEX IDX_FC64968B2AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE grande_ville (
  id                 INT UNSIGNED AUTO_INCREMENT NOT NULL,
  coordonnees_gps_id INT UNSIGNED DEFAULT NULL,
  UNIQUE INDEX UNIQ_DEC3C45D63A2FADA (coordonnees_gps_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_secteur (
  station_id INT UNSIGNED NOT NULL,
  secteur_id INT UNSIGNED NOT NULL,
  INDEX IDX_C9E3804121BDB235 (station_id),
  INDEX IDX_C9E380419F7E4405 (secteur_id),
  PRIMARY KEY (station_id, secteur_id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_zone_touristique (
  station_id          INT UNSIGNED NOT NULL,
  zone_touristique_id INT UNSIGNED NOT NULL,
  INDEX IDX_46AD805C21BDB235 (station_id),
  INDEX IDX_46AD805C1314056E (zone_touristique_id),
  PRIMARY KEY (station_id, zone_touristique_id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_profil (
  station_id INT UNSIGNED NOT NULL,
  profil_id  INT UNSIGNED NOT NULL,
  INDEX IDX_2934A17121BDB235 (station_id),
  INDEX IDX_2934A171275ED078 (profil_id),
  PRIMARY KEY (station_id, profil_id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_comment_venir_grande_ville (
  id                       INT UNSIGNED AUTO_INCREMENT NOT NULL,
  station_comment_venir_id INT UNSIGNED DEFAULT NULL,
  grande_ville_id          INT UNSIGNED DEFAULT NULL,
  INDEX IDX_54F656DF612C86E5 (station_comment_venir_id),
  INDEX IDX_54F656DFAFDEF94D (grande_ville_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE grande_ville_traduction
  ADD CONSTRAINT FK_FC64968BAFDEF94D FOREIGN KEY (grande_ville_id) REFERENCES grande_ville (id);
ALTER TABLE grande_ville_traduction
  ADD CONSTRAINT FK_FC64968B2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE grande_ville
  ADD CONSTRAINT FK_DEC3C45D63A2FADA FOREIGN KEY (coordonnees_gps_id) REFERENCES coordonnees_gps (id);
ALTER TABLE station_secteur
  ADD CONSTRAINT FK_C9E3804121BDB235 FOREIGN KEY (station_id) REFERENCES station (id)
  ON DELETE CASCADE;
ALTER TABLE station_secteur
  ADD CONSTRAINT FK_C9E380419F7E4405 FOREIGN KEY (secteur_id) REFERENCES secteur (id)
  ON DELETE CASCADE;
ALTER TABLE station_zone_touristique
  ADD CONSTRAINT FK_46AD805C21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)
  ON DELETE CASCADE;
ALTER TABLE station_zone_touristique
  ADD CONSTRAINT FK_46AD805C1314056E FOREIGN KEY (zone_touristique_id) REFERENCES zone_touristique (id)
  ON DELETE CASCADE;
ALTER TABLE station_profil
  ADD CONSTRAINT FK_2934A17121BDB235 FOREIGN KEY (station_id) REFERENCES station (id)
  ON DELETE CASCADE;
ALTER TABLE station_profil
  ADD CONSTRAINT FK_2934A171275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)
  ON DELETE CASCADE;
ALTER TABLE station_comment_venir_grande_ville
  ADD CONSTRAINT FK_54F656DF612C86E5 FOREIGN KEY (station_comment_venir_id) REFERENCES station_comment_venir (id);
ALTER TABLE station_comment_venir_grande_ville
  ADD CONSTRAINT FK_54F656DFAFDEF94D FOREIGN KEY (grande_ville_id) REFERENCES grande_ville (id);
ALTER TABLE station
  DROP FOREIGN KEY FK_9F39F8B11314056E;
ALTER TABLE station
  DROP FOREIGN KEY FK_9F39F8B19F7E4405;
DROP INDEX IDX_9F39F8B11314056E
ON station;
DROP INDEX IDX_9F39F8B19F7E4405
ON station;
ALTER TABLE station
  ADD station_mere_id INT UNSIGNED DEFAULT NULL,
  DROP zone_touristique_id,
  DROP secteur_id;
ALTER TABLE station
  ADD CONSTRAINT FK_9F39F8B170786150 FOREIGN KEY (station_mere_id) REFERENCES station (id);
CREATE INDEX IDX_9F39F8B170786150
  ON station (station_mere_id);
ALTER TABLE station_carte_identite
  ADD adresse_id INT UNSIGNED DEFAULT NULL,
  DROP code_postal,
  DROP lien_meteo;
ALTER TABLE station_carte_identite
  ADD CONSTRAINT FK_D63BAAB34DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id);
CREATE UNIQUE INDEX UNIQ_D63BAAB34DE7DC5C
  ON station_carte_identite (adresse_id);
ALTER TABLE tarif
  CHANGE valeur valeur NUMERIC(7, 2) DEFAULT NULL;
ALTER TABLE distance
  CHANGE valeur valeur DOUBLE PRECISION NOT NULL;
ALTER TABLE description_forfait_ski
  CHANGE quantite quantite DOUBLE PRECISION NOT NULL;
ALTER TABLE ligne_description_forfait_ski
  CHANGE quantite quantite DOUBLE PRECISION NOT NULL;
