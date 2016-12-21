/*CREATE TABLE decote (id INT UNSIGNED AUTO_INCREMENT NOT NULL, decote_periode_validite_date_id INT UNSIGNED DEFAULT NULL, decote_periode_validite_jour_id INT UNSIGNED DEFAULT NULL, decote_periode_sejour_date_id INT UNSIGNED DEFAULT NULL, decote_unifie_id INT UNSIGNED DEFAULT NULL, site_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, libelle VARCHAR(255) NOT NULL, type_remise SMALLINT UNSIGNED NOT NULL, valeur_remise NUMERIC(10, 2) DEFAULT '0' NOT NULL, type_periode_validite SMALLINT UNSIGNED NOT NULL, type_periode_sejour SMALLINT UNSIGNED NOT NULL, type_application SMALLINT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_6FE6E907FE886D8D (decote_periode_validite_date_id), UNIQUE INDEX UNIQ_6FE6E90764133136 (decote_periode_validite_jour_id), UNIQUE INDEX UNIQ_6FE6E907FF26DB0A (decote_periode_sejour_date_id), INDEX IDX_6FE6E907599156DD (decote_unifie_id), INDEX IDX_6FE6E907F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_type_fournisseur (decote_id INT UNSIGNED NOT NULL, famille_prestation_annexe_id INT UNSIGNED NOT NULL, INDEX IDX_B8192084A5DBDC32 (decote_id), INDEX IDX_B81920845D1D40E4 (famille_prestation_annexe_id), PRIMARY KEY(decote_id, famille_prestation_annexe_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_periode_validite (decote_id INT UNSIGNED NOT NULL, periode_validite_id INT UNSIGNED NOT NULL, INDEX IDX_C507288A5DBDC32 (decote_id), INDEX IDX_C507288BF5863D9 (periode_validite_id), PRIMARY KEY(decote_id, periode_validite_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_famille_prestation_annexe (famille_prestation_annexe_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, decote_id INT UNSIGNED NOT NULL, INDEX IDX_71D7B62F5D1D40E4 (famille_prestation_annexe_id), INDEX IDX_71D7B62F670C757F (fournisseur_id), INDEX IDX_71D7B62FA5DBDC32 (decote_id), PRIMARY KEY(famille_prestation_annexe_id, fournisseur_id, decote_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_fournisseur (type SMALLINT UNSIGNED NOT NULL, decote_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, INDEX IDX_F9E53F22A5DBDC32 (decote_id), INDEX IDX_F9E53F22670C757F (fournisseur_id), PRIMARY KEY(type, decote_id, fournisseur_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_fournisseur_prestation_annexe (fournisseur_prestation_annexe_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, decote_id INT UNSIGNED NOT NULL, INDEX IDX_CBC51E43DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_CBC51E43670C757F (fournisseur_id), INDEX IDX_CBC51E43A5DBDC32 (decote_id), PRIMARY KEY(fournisseur_prestation_annexe_id, fournisseur_id, decote_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_hebergement (hebergement_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, decote_id INT UNSIGNED NOT NULL, INDEX IDX_8729288C23BB0F66 (hebergement_id), INDEX IDX_8729288C670C757F (fournisseur_id), INDEX IDX_8729288CA5DBDC32 (decote_id), PRIMARY KEY(hebergement_id, fournisseur_id, decote_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_logement_periode (decote_id INT UNSIGNED NOT NULL, periode_id INT UNSIGNED NOT NULL, logement_id INT UNSIGNED NOT NULL, INDEX IDX_2C8A0086A5DBDC32 (decote_id), INDEX IDX_2C8A0086F384C1CF (periode_id), INDEX IDX_2C8A008658ABF955 (logement_id), PRIMARY KEY(decote_id, periode_id, logement_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_periode_sejour_date (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_periode_validite_date (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_periode_validite_jour (id INT UNSIGNED AUTO_INCREMENT NOT NULL, jour_debut INT UNSIGNED NOT NULL, jour_fin INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_station (station_id INT UNSIGNED NOT NULL, fournisseur_id INT UNSIGNED NOT NULL, decote_id INT UNSIGNED NOT NULL, INDEX IDX_ACF3EB1221BDB235 (station_id), INDEX IDX_ACF3EB12670C757F (fournisseur_id), INDEX IDX_ACF3EB12A5DBDC32 (decote_id), PRIMARY KEY(station_id, fournisseur_id, decote_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_type_affectation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, decote_id INT UNSIGNED DEFAULT NULL, type_affectation SMALLINT NOT NULL, INDEX IDX_7A5A8B65A5DBDC32 (decote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE decote_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE decote ADD CONSTRAINT FK_6FE6E907FE886D8D FOREIGN KEY (decote_periode_validite_date_id) REFERENCES decote_periode_validite_date (id);
ALTER TABLE decote ADD CONSTRAINT FK_6FE6E90764133136 FOREIGN KEY (decote_periode_validite_jour_id) REFERENCES decote_periode_validite_jour (id);
ALTER TABLE decote ADD CONSTRAINT FK_6FE6E907FF26DB0A FOREIGN KEY (decote_periode_sejour_date_id) REFERENCES decote_periode_sejour_date (id);
ALTER TABLE decote ADD CONSTRAINT FK_6FE6E907599156DD FOREIGN KEY (decote_unifie_id) REFERENCES decote_unifie (id);
ALTER TABLE decote ADD CONSTRAINT FK_6FE6E907F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE decote_type_fournisseur ADD CONSTRAINT FK_B8192084A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id) ON DELETE CASCADE;
ALTER TABLE decote_type_fournisseur ADD CONSTRAINT FK_B81920845D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id) ON DELETE CASCADE;
ALTER TABLE decote_periode_validite ADD CONSTRAINT FK_C507288A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id) ON DELETE CASCADE;
ALTER TABLE decote_periode_validite ADD CONSTRAINT FK_C507288BF5863D9 FOREIGN KEY (periode_validite_id) REFERENCES periode_validite (id) ON DELETE CASCADE;
ALTER TABLE decote_famille_prestation_annexe ADD CONSTRAINT FK_71D7B62F5D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE decote_famille_prestation_annexe ADD CONSTRAINT FK_71D7B62F670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE decote_famille_prestation_annexe ADD CONSTRAINT FK_71D7B62FA5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);
ALTER TABLE decote_fournisseur ADD CONSTRAINT FK_F9E53F22A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);
ALTER TABLE decote_fournisseur ADD CONSTRAINT FK_F9E53F22670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE decote_fournisseur_prestation_annexe ADD CONSTRAINT FK_CBC51E43DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE decote_fournisseur_prestation_annexe ADD CONSTRAINT FK_CBC51E43670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE decote_fournisseur_prestation_annexe ADD CONSTRAINT FK_CBC51E43A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);
ALTER TABLE decote_hebergement ADD CONSTRAINT FK_8729288C23BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE decote_hebergement ADD CONSTRAINT FK_8729288C670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE decote_hebergement ADD CONSTRAINT FK_8729288CA5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);
ALTER TABLE decote_logement_periode ADD CONSTRAINT FK_2C8A0086A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);
ALTER TABLE decote_logement_periode ADD CONSTRAINT FK_2C8A0086F384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id);
ALTER TABLE decote_logement_periode ADD CONSTRAINT FK_2C8A008658ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE decote_station ADD CONSTRAINT FK_ACF3EB1221BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
ALTER TABLE decote_station ADD CONSTRAINT FK_ACF3EB12670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE decote_station ADD CONSTRAINT FK_ACF3EB12A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);
ALTER TABLE decote_type_affectation ADD CONSTRAINT FK_7A5A8B65A5DBDC32 FOREIGN KEY (decote_id) REFERENCES decote (id);*/

/*ALTER TABLE decote ADD type SMALLINT UNSIGNED NOT NULL;*/

CREATE TABLE canal_decote (id INT UNSIGNED AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
