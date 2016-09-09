DROP TABLE IF EXISTS fournisseur_prestation_annexe;

CREATE TABLE IF NOT EXISTS fournisseur_prestation_annexe_capacite (id INT UNSIGNED AUTO_INCREMENT NOT NULL, min INT NOT NULL, max INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS fournisseur_prestation_annexe_duree_sejour (id INT UNSIGNED AUTO_INCREMENT NOT NULL, min INT NOT NULL, max INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS periode_validite (id INT UNSIGNED AUTO_INCREMENT NOT NULL, tarif_id INT UNSIGNED DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_C9CE4FAF357C0A59 (tarif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS prestation_annexe_tarif (id INT UNSIGNED AUTO_INCREMENT NOT NULL, prestation_annexe_id INT UNSIGNED DEFAULT NULL, prix_public NUMERIC(10, 2) NOT NULL, INDEX IDX_D1663A6833AD7BEF (prestation_annexe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS fournisseur_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, capacite_id INT UNSIGNED DEFAULT NULL, duree_sejour_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, type INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_9AB97BB37C79189D (capacite_id), UNIQUE INDEX UNIQ_9AB97BB39CDD2F46 (duree_sejour_id), INDEX IDX_9AB97BB3670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE periode_validite ADD CONSTRAINT FK_C9CE4FAF357C0A59 FOREIGN KEY (tarif_id) REFERENCES prestation_annexe_tarif (id);
ALTER TABLE prestation_annexe_tarif ADD CONSTRAINT FK_D1663A6833AD7BEF FOREIGN KEY (prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);

ALTER TABLE fournisseur_prestation_annexe ADD CONSTRAINT FK_9AB97BB37C79189D FOREIGN KEY (capacite_id) REFERENCES fournisseur_prestation_annexe_capacite (id);
ALTER TABLE fournisseur_prestation_annexe ADD CONSTRAINT FK_9AB97BB39CDD2F46 FOREIGN KEY (duree_sejour_id) REFERENCES fournisseur_prestation_annexe_duree_sejour (id);
ALTER TABLE fournisseur_prestation_annexe ADD CONSTRAINT FK_9AB97BB3670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);