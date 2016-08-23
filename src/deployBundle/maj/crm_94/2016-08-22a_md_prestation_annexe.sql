CREATE TABLE famille_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE famille_prestation_annexe_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, famille_prestation_annexe_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_59030F785D1D40E4 (famille_prestation_annexe_id), INDEX IDX_59030F782AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE sous_famille_prestation_annexe (id INT UNSIGNED AUTO_INCREMENT NOT NULL, famille_prestation_annexe_id INT UNSIGNED DEFAULT NULL, INDEX IDX_F81CF9D05D1D40E4 (famille_prestation_annexe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE sous_famille_prestation_annexe_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, sous_famille_prestation_annexe_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_29BB02A23AB7F643 (sous_famille_prestation_annexe_id), INDEX IDX_29BB02A22AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE famille_prestation_annexe_traduction ADD CONSTRAINT FK_59030F785D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE famille_prestation_annexe_traduction ADD CONSTRAINT FK_59030F782AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE sous_famille_prestation_annexe ADD CONSTRAINT FK_F81CF9D05D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE sous_famille_prestation_annexe_traduction ADD CONSTRAINT FK_29BB02A23AB7F643 FOREIGN KEY (sous_famille_prestation_annexe_id) REFERENCES sous_famille_prestation_annexe (id);
ALTER TABLE sous_famille_prestation_annexe_traduction ADD CONSTRAINT FK_29BB02A22AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);