CREATE TABLE code_promo_fournisseur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED DEFAULT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, code_promo_fournisseur_unifie_id INT UNSIGNED DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_81945EBCF6BD1646 (site_id), INDEX IDX_81945EBC294102D4 (code_promo_id), INDEX IDX_81945EBC670C757F (fournisseur_id), INDEX IDX_81945EBCBE68C0E0 (code_promo_fournisseur_unifie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE code_promo_fournisseur_unifie (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE code_promo_fournisseur ADD CONSTRAINT FK_81945EBCF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE code_promo_fournisseur ADD CONSTRAINT FK_81945EBC294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);
ALTER TABLE code_promo_fournisseur ADD CONSTRAINT FK_81945EBC670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE code_promo_fournisseur ADD CONSTRAINT FK_81945EBCBE68C0E0 FOREIGN KEY (code_promo_fournisseur_unifie_id) REFERENCES code_promo_fournisseur_unifie (id);

CREATE UNIQUE INDEX UNIQ_F74ED340C05FB297 ON interlocuteur_user (confirmation_token);
CREATE UNIQUE INDEX UNIQ_4412BA73C05FB297 ON utilisateur_user (confirmation_token);
CREATE UNIQUE INDEX UNIQ_5C0F152BC05FB297 ON client_user (confirmation_token);