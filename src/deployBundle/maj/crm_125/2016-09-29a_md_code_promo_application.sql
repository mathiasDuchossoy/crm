CREATE TABLE code_promo_fournisseur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, type SMALLINT UNSIGNED NOT NULL, INDEX IDX_81945EBC670C757F (fournisseur_id), INDEX IDX_81945EBC294102D4 (code_promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE code_promo_fournisseur ADD CONSTRAINT FK_81945EBC670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE code_promo_fournisseur ADD CONSTRAINT FK_81945EBC294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);

CREATE TABLE code_promo_hebergement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hebergement_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, INDEX IDX_FF58491223BB0F66 (hebergement_id), INDEX IDX_FF584912670C757F (fournisseur_id), INDEX IDX_FF584912294102D4 (code_promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE code_promo_hebergement ADD CONSTRAINT FK_FF58491223BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE code_promo_hebergement ADD CONSTRAINT FK_FF584912670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE code_promo_hebergement ADD CONSTRAINT FK_FF584912294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);


CREATE TABLE code_promo_logement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, logement_id INT UNSIGNED DEFAULT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, INDEX IDX_731BA69558ABF955 (logement_id), INDEX IDX_731BA695294102D4 (code_promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE code_promo_logement ADD CONSTRAINT FK_731BA69558ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE code_promo_logement ADD CONSTRAINT FK_731BA695294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);

CREATE TABLE code_promo_fournisseur_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, INDEX IDX_1EB81EBDDF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_1EB81EBD670C757F (fournisseur_id), INDEX IDX_1EB81EBD294102D4 (code_promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE code_promo_fournisseur_prestation_annexe ADD CONSTRAINT FK_1EB81EBDDF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE code_promo_fournisseur_prestation_annexe ADD CONSTRAINT FK_1EB81EBD670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE code_promo_fournisseur_prestation_annexe ADD CONSTRAINT FK_1EB81EBD294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);


CREATE TABLE code_promo_famille_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, famille_prestation_annexe_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, INDEX IDX_77E64DD75D1D40E4 (famille_prestation_annexe_id), INDEX IDX_77E64DD7670C757F (fournisseur_id), INDEX IDX_77E64DD7294102D4 (code_promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE code_promo_famille_prestation_annexe ADD CONSTRAINT FK_77E64DD75D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE code_promo_famille_prestation_annexe ADD CONSTRAINT FK_77E64DD7670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE code_promo_famille_prestation_annexe ADD CONSTRAINT FK_77E64DD7294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);

