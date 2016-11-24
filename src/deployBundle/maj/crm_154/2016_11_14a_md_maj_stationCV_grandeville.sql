DROP TABLE IF EXISTS `grande_ville_traduction`;
DROP TABLE IF EXISTS `station_comment_venir_grande_ville`;
DROP TABLE IF EXISTS `grande_ville`;

CREATE TABLE grande_ville (id INT UNSIGNED AUTO_INCREMENT NOT NULL, coordonnees_gps_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_DEC3C45D63A2FADA (coordonnees_gps_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE grande_ville_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, grande_ville_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_FC64968BAFDEF94D (grande_ville_id), INDEX IDX_FC64968B2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE station_comment_venir_grande_ville (station_comment_venir_id INT UNSIGNED NOT NULL, grande_ville_id INT UNSIGNED NOT NULL, INDEX IDX_54F656DF612C86E5 (station_comment_venir_id), INDEX IDX_54F656DFAFDEF94D (grande_ville_id), PRIMARY KEY(station_comment_venir_id, grande_ville_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE grande_ville ADD CONSTRAINT FK_DEC3C45D63A2FADA FOREIGN KEY (coordonnees_gps_id) REFERENCES coordonnees_gps (id);
ALTER TABLE grande_ville_traduction ADD CONSTRAINT FK_FC64968BAFDEF94D FOREIGN KEY (grande_ville_id) REFERENCES grande_ville (id);
ALTER TABLE grande_ville_traduction ADD CONSTRAINT FK_FC64968B2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE station_comment_venir_grande_ville ADD CONSTRAINT FK_54F656DF612C86E5 FOREIGN KEY (station_comment_venir_id) REFERENCES station_comment_venir (id) ON DELETE CASCADE;
ALTER TABLE station_comment_venir_grande_ville ADD CONSTRAINT FK_54F656DFAFDEF94D FOREIGN KEY (grande_ville_id) REFERENCES grande_ville (id) ON DELETE CASCADE;