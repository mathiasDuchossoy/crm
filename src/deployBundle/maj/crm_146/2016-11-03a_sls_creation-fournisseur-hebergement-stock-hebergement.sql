DROP TABLE IF EXISTS fournisseur_prestation_annexe_stock;
CREATE TABLE fournisseur_prestation_annexe_stock_hebergement (fournisseur_prestation_annexe_id INT UNSIGNED NOT NULL, periode_id INT UNSIGNED NOT NULL, fournisseur_hebergement_id INT UNSIGNED NOT NULL, stock INT UNSIGNED NOT NULL, INDEX IDX_A07B7CC1DF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_A07B7CC1F384C1CF (periode_id), INDEX IDX_A07B7CC19E819CB8 (fournisseur_hebergement_id), PRIMARY KEY(fournisseur_prestation_annexe_id, periode_id, fournisseur_hebergement_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_prestation_annexe_stock_hebergement ADD CONSTRAINT FK_A07B7CC1DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE fournisseur_prestation_annexe_stock_hebergement ADD CONSTRAINT FK_A07B7CC1F384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id);
ALTER TABLE fournisseur_prestation_annexe_stock_hebergement ADD CONSTRAINT FK_A07B7CC19E819CB8 FOREIGN KEY (fournisseur_hebergement_id) REFERENCES fournisseur_hebergement (id);