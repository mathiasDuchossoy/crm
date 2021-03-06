CREATE TABLE prestation_annexe_fournisseur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED DEFAULT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, prestation_annexe_fournisseur_unifie_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_F8A26796F6BD1646 (site_id), INDEX IDX_F8A26796DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_F8A267965ED7EA72 (prestation_annexe_fournisseur_unifie_id), INDEX IDX_F8A26796670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_fournisseur_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_hebergement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED DEFAULT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, prestation_annexe_hebergement_unifie_id INT UNSIGNED DEFAULT NULL, hebergement_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_866E7038F6BD1646 (site_id), INDEX IDX_866E7038DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_866E7038B0B906A6 (prestation_annexe_hebergement_unifie_id), INDEX IDX_866E703823BB0F66 (hebergement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_hebergement_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_logement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED DEFAULT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, prestation_annexe_logement_unifie_id INT UNSIGNED DEFAULT NULL, logement_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_647C0B63F6BD1646 (site_id), INDEX IDX_647C0B63DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_647C0B63291AA779 (prestation_annexe_logement_unifie_id), INDEX IDX_647C0B6358ABF955 (logement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_logement_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_station (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED DEFAULT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, prestation_annexe_station_unifie_id INT UNSIGNED DEFAULT NULL, station_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_7A4D4838F6BD1646 (site_id), INDEX IDX_7A4D4838DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_7A4D4838F1775174 (prestation_annexe_station_unifie_id), INDEX IDX_7A4D483821BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE prestation_annexe_station_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE prestation_annexe_fournisseur ADD CONSTRAINT FK_F8A26796F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE prestation_annexe_fournisseur ADD CONSTRAINT FK_F8A26796DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE prestation_annexe_fournisseur ADD CONSTRAINT FK_F8A267965ED7EA72 FOREIGN KEY (prestation_annexe_fournisseur_unifie_id) REFERENCES prestation_annexe_fournisseur_unifie (id);
ALTER TABLE prestation_annexe_fournisseur ADD CONSTRAINT FK_F8A26796670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE prestation_annexe_hebergement ADD CONSTRAINT FK_866E7038F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE prestation_annexe_hebergement ADD CONSTRAINT FK_866E7038DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE prestation_annexe_hebergement ADD CONSTRAINT FK_866E7038B0B906A6 FOREIGN KEY (prestation_annexe_hebergement_unifie_id) REFERENCES prestation_annexe_hebergement_unifie (id);
ALTER TABLE prestation_annexe_hebergement ADD CONSTRAINT FK_866E703823BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE prestation_annexe_logement ADD CONSTRAINT FK_647C0B63F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE prestation_annexe_logement ADD CONSTRAINT FK_647C0B63DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE prestation_annexe_logement ADD CONSTRAINT FK_647C0B63291AA779 FOREIGN KEY (prestation_annexe_logement_unifie_id) REFERENCES prestation_annexe_logement_unifie (id);
ALTER TABLE prestation_annexe_logement ADD CONSTRAINT FK_647C0B6358ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE prestation_annexe_station ADD CONSTRAINT FK_7A4D4838F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE prestation_annexe_station ADD CONSTRAINT FK_7A4D4838DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE prestation_annexe_station ADD CONSTRAINT FK_7A4D4838F1775174 FOREIGN KEY (prestation_annexe_station_unifie_id) REFERENCES prestation_annexe_station_unifie (id);
ALTER TABLE prestation_annexe_station ADD CONSTRAINT FK_7A4D483821BDB235 FOREIGN KEY (station_id) REFERENCES station (id);

ALTER TABLE fournisseur_prestation_annexe ADD mode_affectation INT UNSIGNED NOT NULL;


ALTER TABLE prestation_annexe_hebergement ADD fournisseur_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_hebergement ADD CONSTRAINT FK_866E7038670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
CREATE INDEX IDX_866E7038670C757F ON prestation_annexe_hebergement (fournisseur_id);


ALTER TABLE prestation_annexe_fournisseur ADD station_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_fournisseur ADD CONSTRAINT FK_F8A2679621BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
CREATE INDEX IDX_F8A2679621BDB235 ON prestation_annexe_fournisseur (station_id);