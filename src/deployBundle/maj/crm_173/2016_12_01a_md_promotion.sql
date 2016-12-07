/*CREATE TABLE promotion (id INT UNSIGNED AUTO_INCREMENT NOT NULL, promotion_unifie_id INT UNSIGNED DEFAULT NULL, site_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, libelle VARCHAR(255) NOT NULL, type_remise SMALLINT NOT NULL, valeur_remise NUMERIC(10, 2) NOT NULL, INDEX IDX_C11D7DD129721ACE (promotion_unifie_id), INDEX IDX_C11D7DD1F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD129721ACE FOREIGN KEY (promotion_unifie_id) REFERENCES promotion_unifie (id);
ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD1F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);*/

/*
ALTER TABLE promotion ADD type_periode_sejour SMALLINT UNSIGNED NOT NULL, CHANGE type_remise type_remise SMALLINT UNSIGNED NOT NULL, CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;
*/

/*CREATE TABLE promotion_type_affectation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, promotion_id INT UNSIGNED DEFAULT NULL, type_affectation SMALLINT NOT NULL, INDEX IDX_50BDDAB2139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_type_affectation ADD CONSTRAINT FK_50BDDAB2139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE code_promo_application CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE promotion ADD type_application SMALLINT UNSIGNED NOT NULL, CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*CREATE TABLE promotion_famille_prestation_annexe (promotion_id INT UNSIGNED NOT NULL, famille_prestation_annexe_id INT UNSIGNED NOT NULL, INDEX IDX_F80DD74E139DF194 (promotion_id), INDEX IDX_F80DD74E5D1D40E4 (famille_prestation_annexe_id), PRIMARY KEY(promotion_id, famille_prestation_annexe_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE;
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E5D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id) ON DELETE CASCADE;
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*CREATE TABLE promotion_type_fournisseur (promotion_id INT UNSIGNED NOT NULL, famille_prestation_annexe_id INT UNSIGNED NOT NULL, INDEX IDX_92FE7153139DF194 (promotion_id), INDEX IDX_92FE71535D1D40E4 (famille_prestation_annexe_id), PRIMARY KEY(promotion_id, famille_prestation_annexe_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_famille_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, famille_prestation_annexe_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, promotion_id INT UNSIGNED DEFAULT NULL, INDEX IDX_F80DD74E5D1D40E4 (famille_prestation_annexe_id), INDEX IDX_F80DD74E670C757F (fournisseur_id), INDEX IDX_F80DD74E139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE promotion_fournisseur_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, promotion_id INT UNSIGNED DEFAULT NULL, INDEX IDX_EEF93923DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_EEF93923670C757F (fournisseur_id), INDEX IDX_EEF93923139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_type_fournisseur ADD CONSTRAINT FK_92FE7153139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE;
ALTER TABLE promotion_type_fournisseur ADD CONSTRAINT FK_92FE71535D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id) ON DELETE CASCADE;
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E5D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_famille_prestation_annexe ADD CONSTRAINT FK_F80DD74E139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_fournisseur_prestation_annexe ADD CONSTRAINT FK_EEF93923DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE promotion_fournisseur_prestation_annexe ADD CONSTRAINT FK_EEF93923670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_fournisseur_prestation_annexe ADD CONSTRAINT FK_EEF93923139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*
CREATE TABLE promotion_fournisseur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, promotion_id INT UNSIGNED DEFAULT NULL, type SMALLINT UNSIGNED NOT NULL, INDEX IDX_E76003E8670C757F (fournisseur_id), INDEX IDX_E76003E8139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_fournisseur ADD CONSTRAINT FK_E76003E8670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_fournisseur ADD CONSTRAINT FK_E76003E8139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;
*/

/*CREATE TABLE promotion_hebergement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hebergement_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, promotion_id INT UNSIGNED DEFAULT NULL, INDEX IDX_99AC144623BB0F66 (hebergement_id), INDEX IDX_99AC1446670C757F (fournisseur_id), INDEX IDX_99AC1446139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_hebergement ADD CONSTRAINT FK_99AC144623BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE promotion_hebergement ADD CONSTRAINT FK_99AC1446670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_hebergement ADD CONSTRAINT FK_99AC1446139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*CREATE TABLE promotion_logement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, logement_id INT UNSIGNED DEFAULT NULL, promotion_id INT UNSIGNED DEFAULT NULL, INDEX IDX_36A9E01358ABF955 (logement_id), INDEX IDX_36A9E013139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_logement ADD CONSTRAINT FK_36A9E01358ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE promotion_logement ADD CONSTRAINT FK_36A9E013139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*CREATE TABLE promotion_periode_validite (promotion_id INT UNSIGNED NOT NULL, periode_validite_id INT UNSIGNED NOT NULL, INDEX IDX_26B7235F139DF194 (promotion_id), INDEX IDX_26B7235FBF5863D9 (periode_validite_id), PRIMARY KEY(promotion_id, periode_validite_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_periode_validite ADD CONSTRAINT FK_26B7235F139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE;
ALTER TABLE promotion_periode_validite ADD CONSTRAINT FK_26B7235FBF5863D9 FOREIGN KEY (periode_validite_id) REFERENCES periode_validite (id) ON DELETE CASCADE;
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*CREATE TABLE promotion_logement_periode (promotion_id INT UNSIGNED NOT NULL, periode_id INT UNSIGNED NOT NULL, logement_id INT UNSIGNED NOT NULL, INDEX IDX_66D5151139DF194 (promotion_id), INDEX IDX_66D5151F384C1CF (periode_id), INDEX IDX_66D515158ABF955 (logement_id), PRIMARY KEY(promotion_id, periode_id, logement_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_logement_periode ADD CONSTRAINT FK_66D5151139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
ALTER TABLE promotion_logement_periode ADD CONSTRAINT FK_66D5151F384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id);
ALTER TABLE promotion_logement_periode ADD CONSTRAINT FK_66D515158ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE promotion CHANGE valeur_remise valeur_remise NUMERIC(10, 2) NOT NULL;*/

/*ALTER TABLE promotion ADD valeur_remise NUMERIC(10, 2) DEFAULT '0' NOT NULL;*/

CREATE TABLE promotion_station (id INT UNSIGNED AUTO_INCREMENT NOT NULL, station_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, promotion_id INT UNSIGNED DEFAULT NULL, INDEX IDX_C7440E8B21BDB235 (station_id), INDEX IDX_C7440E8B670C757F (fournisseur_id), INDEX IDX_C7440E8B139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE promotion_station ADD CONSTRAINT FK_C7440E8B21BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
ALTER TABLE promotion_station ADD CONSTRAINT FK_C7440E8B670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE promotion_station ADD CONSTRAINT FK_C7440E8B139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id);
