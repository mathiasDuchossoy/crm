ALTER TABLE fournisseur_prestation_annexe ADD free_sale TINYINT(1) DEFAULT '0' NOT NULL;

CREATE TABLE fournisseur_prestation_annexe_periode_indisponible (id INT UNSIGNED AUTO_INCREMENT NOT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_53D57F80DF2F2EF6 (fournisseur_prestation_annexe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_prestation_annexe_periode_indisponible ADD CONSTRAINT FK_53D57F80DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);

ALTER TABLE fournisseur_prestation_annexe_param ADD forfait_quantite_type INT UNSIGNED DEFAULT NULL;
