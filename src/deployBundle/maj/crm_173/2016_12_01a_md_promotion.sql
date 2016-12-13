CREATE TABLE logement_type_periode (logement_id INT UNSIGNED NOT NULL, type_periode_id INT UNSIGNED NOT NULL, INDEX IDX_F43E3BFB58ABF955 (logement_id), INDEX IDX_F43E3BFBEE8717EA (type_periode_id), PRIMARY KEY(logement_id, type_periode_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE nombre_de_chambre (id INT UNSIGNED AUTO_INCREMENT NOT NULL, classement INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE nombre_de_chambre_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, nombre_de_chambre_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_D18D499F17036B1E (nombre_de_chambre_id), INDEX IDX_D18D499F2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion (id INT UNSIGNED AUTO_INCREMENT NOT NULL, promotion_periode_validite_date_id INT UNSIGNED DEFAULT NULL, promotion_periode_validite_jour_id INT UNSIGNED DEFAULT NULL, promotion_unifie_id INT UNSIGNED DEFAULT NULL, site_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, libelle VARCHAR(255) NOT NULL, type_remise SMALLINT UNSIGNED NOT NULL, valeur_remise NUMERIC(10, 2) DEFAULT '0' NOT NULL, type_periode_sejour SMALLINT UNSIGNED NOT NULL, type_application SMALLINT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_C11D7DD1F7C2EC14 (promotion_periode_validite_date_id), UNIQUE INDEX UNIQ_C11D7DD16D59B0AF (promotion_periode_validite_jour_id), INDEX IDX_C11D7DD129721ACE (promotion_unifie_id), INDEX IDX_C11D7DD1F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_type_fournisseur (promotion_id INT UNSIGNED NOT NULL, famille_prestation_annexe_id INT UNSIGNED NOT NULL, INDEX IDX_92FE7153139DF194 (promotion_id), INDEX IDX_92FE71535D1D40E4 (famille_prestation_annexe_id), PRIMARY KEY(promotion_id, famille_prestation_annexe_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_periode_validite (promotion_id INT UNSIGNED NOT NULL, periode_validite_id INT UNSIGNED NOT NULL, INDEX IDX_26B7235F139DF194 (promotion_id), INDEX IDX_26B7235FBF5863D9 (periode_validite_id), PRIMARY KEY(promotion_id, periode_validite_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_famille_prestation_annexe (famille_prestation_annexe_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, promotion_id INT UNSIGNED NOT NULL, INDEX IDX_F80DD74E5D1D40E4 (famille_prestation_annexe_id), INDEX IDX_F80DD74E670C757F (fournisseur_id), INDEX IDX_F80DD74E139DF194 (promotion_id), PRIMARY KEY(famille_prestation_annexe_id, fournisseur_id, promotion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_fournisseur (type SMALLINT UNSIGNED NOT NULL, promotion_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, INDEX IDX_E76003E8139DF194 (promotion_id), INDEX IDX_E76003E8670C757F (fournisseur_id), PRIMARY KEY(type, promotion_id, fournisseur_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_fournisseur_prestation_annexe (fournisseur_prestation_annexe_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, promotion_id INT UNSIGNED NOT NULL, INDEX IDX_EEF93923DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_EEF93923670C757F (fournisseur_id), INDEX IDX_EEF93923139DF194 (promotion_id), PRIMARY KEY(fournisseur_prestation_annexe_id, fournisseur_id, promotion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_hebergement (hebergement_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, promotion_id INT UNSIGNED NOT NULL, INDEX IDX_99AC144623BB0F66 (hebergement_id), INDEX IDX_99AC1446670C757F (fournisseur_id), INDEX IDX_99AC1446139DF194 (promotion_id), PRIMARY KEY(hebergement_id, fournisseur_id, promotion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_logement_periode (promotion_id INT UNSIGNED NOT NULL, periode_id INT UNSIGNED NOT NULL, logement_id INT UNSIGNED NOT NULL, INDEX IDX_66D5151139DF194 (promotion_id), INDEX IDX_66D5151F384C1CF (periode_id), INDEX IDX_66D515158ABF955 (logement_id), PRIMARY KEY(promotion_id, periode_id, logement_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_periode_validite_date (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_periode_validite_jour (id INT UNSIGNED AUTO_INCREMENT NOT NULL, jour_debut INT UNSIGNED NOT NULL, jour_fin INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_station (station_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, promotion_id INT UNSIGNED NOT NULL, INDEX IDX_C7440E8B21BDB235 (station_id), INDEX IDX_C7440E8B670C757F (fournisseur_id), INDEX IDX_C7440E8B139DF194 (promotion_id), PRIMARY KEY(station_id, fournisseur_id, promotion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_type_affectation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, promotion_id INT UNSIGNED DEFAULT NULL, type_affectation SMALLINT NOT NULL, INDEX IDX_50BDDAB2139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE logement_type_periode ADD CONSTRAINT FK_F43E3BFB58ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id) ON DELETE CASCADE;
ALTER TABLE logement_type_periode ADD CONSTRAINT FK_F43E3BFBEE8717EA FOREIGN KEY (type_periode_id) REFERENCES type_periode (id) ON DELETE CASCADE;
ALTER TABLE nombre_de_chambre_traduction ADD CONSTRAINT FK_D18D499F17036B1E FOREIGN KEY (nombre_de_chambre_id) REFERENCES nombre_de_chambre (id);
ALTER TABLE nombre_de_chambre_traduction ADD CONSTRAINT FK_D18D499F2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD1F7C2EC14 FOREIGN KEY (promotion_periode_validite_date_id) REFERENCES promotion_periode_validite_date (id);
ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD16D59B0AF FOREIGN KEY (promotion_periode_validite_jour_id) REFERENCES promotion_periode_validite_jour (id);
ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD129721ACE FOREIGN KEY (promotion_unifie_id) REFERENCES promotion_unifie (id);
ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD1F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE promotion_type_fournisseur ADD CONSTRAINT FK_92FE7153139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE;
ALTER TABLE promotion_type_fournisseur ADD CONSTRAINT FK_92FE71535D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id) ON DELETE CASCADE;
ALTER TABLE promotion_periode_validite ADD CONSTRAINT FK_26B7235F139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE;
ALTER TABLE promotion_periode_validite ADD CONSTRAINT FK_26B7235FBF5863D9 FOREIGN KEY (periode_validite_id) REFERENCES periode_validite (id) ON DELETE CASCADE;
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E5D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_fournisseur ADD CONSTRAINT FK_E76003E8139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_fournisseur ADD CONSTRAINT FK_E76003E8670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_fournisseur_prestation_annexe ADD CONSTRAINT FK_EEF93923DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE promotion_fournisseur_prestation_annexe ADD CONSTRAINT FK_EEF93923670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_fournisseur_prestation_annexe ADD CONSTRAINT FK_EEF93923139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_hebergement ADD CONSTRAINT FK_99AC144623BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE promotion_hebergement ADD CONSTRAINT FK_99AC1446670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_hebergement ADD CONSTRAINT FK_99AC1446139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_logement_periode ADD CONSTRAINT FK_66D5151139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_logement_periode ADD CONSTRAINT FK_66D5151F384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id);
ALTER TABLE promotion_logement_periode ADD CONSTRAINT FK_66D515158ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE promotion_station ADD CONSTRAINT FK_C7440E8B21BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
ALTER TABLE promotion_station ADD CONSTRAINT FK_C7440E8B670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_station ADD CONSTRAINT FK_C7440E8B139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_type_affectation ADD CONSTRAINT FK_50BDDAB2139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
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
ALTER TABLE code_promo_application CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE logement_periode_locatif CHANGE prix_public prix_public NUMERIC(10, 2) DEFAULT '0' NOT NULL, CHANGE stock stock INT DEFAULT 0 NOT NULL, CHANGE prix_fournisseur prix_fournisseur NUMERIC(10, 2) DEFAULT '0' NOT NULL, CHANGE prix_achat prix_achat NUMERIC(10, 2) DEFAULT '0' NOT NULL;
