CREATE TABLE logement_type_periode (logement_id INT UNSIGNED NOT NULL, type_periode_id INT UNSIGNED NOT NULL, INDEX IDX_F43E3BFB58ABF955 (logement_id), INDEX IDX_F43E3BFBEE8717EA (type_periode_id), PRIMARY KEY(logement_id, type_periode_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE nombre_de_chambre (id INT UNSIGNED AUTO_INCREMENT NOT NULL, classement INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE nombre_de_chambre_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, nombre_de_chambre_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_D18D499F17036B1E (nombre_de_chambre_id), INDEX IDX_D18D499F2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE logement_type_periode ADD CONSTRAINT FK_F43E3BFB58ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id) ON DELETE CASCADE;
ALTER TABLE logement_type_periode ADD CONSTRAINT FK_F43E3BFBEE8717EA FOREIGN KEY (type_periode_id) REFERENCES type_periode (id) ON DELETE CASCADE;
ALTER TABLE nombre_de_chambre_traduction ADD CONSTRAINT FK_D18D499F17036B1E FOREIGN KEY (nombre_de_chambre_id) REFERENCES nombre_de_chambre (id);
ALTER TABLE nombre_de_chambre_traduction ADD CONSTRAINT FK_D18D499F2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE station ADD type_taxe_sejour INT NOT NULL, ADD taxe_sejour_prix DOUBLE PRECISION DEFAULT NULL, ADD taxe_sejour_age INT DEFAULT NULL;
ALTER TABLE station_comment_venir_grande_ville MODIFY id INT UNSIGNED NOT NULL;
ALTER TABLE station_comment_venir_grande_ville DROP FOREIGN KEY FK_54F656DF612C86E5;
ALTER TABLE station_comment_venir_grande_ville DROP FOREIGN KEY FK_54F656DFAFDEF94D;
ALTER TABLE station_comment_venir_grande_ville DROP PRIMARY KEY;
ALTER TABLE station_comment_venir_grande_ville DROP id, CHANGE station_comment_venir_id station_comment_venir_id INT UNSIGNED NOT NULL, CHANGE grande_ville_id grande_ville_id INT UNSIGNED NOT NULL;
ALTER TABLE station_comment_venir_grande_ville ADD CONSTRAINT FK_54F656DF612C86E5 FOREIGN KEY (station_comment_venir_id) REFERENCES station_comment_venir (id) ON DELETE CASCADE;
ALTER TABLE station_comment_venir_grande_ville ADD CONSTRAINT FK_54F656DFAFDEF94D FOREIGN KEY (grande_ville_id) REFERENCES grande_ville (id) ON DELETE CASCADE;
ALTER TABLE station_comment_venir_grande_ville ADD PRIMARY KEY (station_comment_venir_id, grande_ville_id);
ALTER TABLE fournisseur ADD priorite SMALLINT UNSIGNED NOT NULL;
ALTER TABLE interlocuteur_user DROP locked, DROP expires_at, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL;
ALTER TABLE utilisateur_user DROP locked, DROP expires_at, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_user DROP locked, DROP expires_at, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL;
ALTER TABLE logement ADD nombre_de_chambre_id INT UNSIGNED DEFAULT NULL, DROP nb_chambre;
ALTER TABLE logement ADD CONSTRAINT FK_F0FD445717036B1E FOREIGN KEY (nombre_de_chambre_id) REFERENCES nombre_de_chambre (id);
CREATE INDEX IDX_F0FD445717036B1E ON logement (nombre_de_chambre_id);
ALTER TABLE logement_unifie ADD archive TINYINT(1) DEFAULT '0' NOT NULL, ADD desactive TINYINT(1) DEFAULT '0' NOT NULL;
ALTER TABLE logement_periode_locatif CHANGE prix_public prix_public NUMERIC(10, 2) DEFAULT '0' NOT NULL, CHANGE stock stock INT DEFAULT 0 NOT NULL, CHANGE prix_fournisseur prix_fournisseur NUMERIC(10, 2) DEFAULT '0' NOT NULL, CHANGE prix_achat prix_achat NUMERIC(10, 2) DEFAULT '0' NOT NULL;
