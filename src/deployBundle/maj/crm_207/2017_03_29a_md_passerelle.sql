CREATE TABLE fournisseur_hebergement_saison_code_passerelle (fournisseur_hebergement_id INT UNSIGNED NOT NULL, saison_code_passerelle_id INT NOT NULL, INDEX IDX_21C9F88D9E819CB8 (fournisseur_hebergement_id), INDEX IDX_21C9F88D2D75C1EB (saison_code_passerelle_id), PRIMARY KEY(fournisseur_hebergement_id, saison_code_passerelle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE logement_unifie_saison_code_passerelle (logement_unifie_id INT UNSIGNED NOT NULL, saison_code_passerelle_id INT NOT NULL, INDEX IDX_6B91F918CD3F9E86 (logement_unifie_id), INDEX IDX_6B91F9182D75C1EB (saison_code_passerelle_id), PRIMARY KEY(logement_unifie_id, saison_code_passerelle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE fournisseur_prestation_annexe_saison_code_passerelle (fournisseur_prestation_annexe_id INT UNSIGNED NOT NULL, saison_code_passerelle_id INT NOT NULL, INDEX IDX_E64A62CCDF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_E64A62CC2D75C1EB (saison_code_passerelle_id), PRIMARY KEY(fournisseur_prestation_annexe_id, saison_code_passerelle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE saison_code_passerelle (id INT AUTO_INCREMENT NOT NULL, saison_id INT UNSIGNED DEFAULT NULL, INDEX IDX_D3152958F965414C (saison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE code_passerelle (id INT AUTO_INCREMENT NOT NULL, saison_code_passerelle_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_EBA470652D75C1EB (saison_code_passerelle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE param_passerelle (id INT AUTO_INCREMENT NOT NULL, passerelle_id INT DEFAULT NULL, discr INT NOT NULL, INDEX IDX_6D47B385EBCA6F32 (passerelle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE softbook_type (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE pass (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, classe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE arkiane (id INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE homeresa (id INT NOT NULL, param_d VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE pierre_vacances (id INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE softbook (id INT NOT NULL, type_id INT DEFAULT NULL, param_a VARCHAR(255) NOT NULL, param_b VARCHAR(255) NOT NULL, INDEX IDX_25DA373C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE anite (id INT NOT NULL, param1 VARCHAR(255) NOT NULL, param2 VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_hebergement_saison_code_passerelle ADD CONSTRAINT FK_21C9F88D9E819CB8 FOREIGN KEY (fournisseur_hebergement_id) REFERENCES fournisseur_hebergement (id) ON DELETE CASCADE;
ALTER TABLE fournisseur_hebergement_saison_code_passerelle ADD CONSTRAINT FK_21C9F88D2D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id) ON DELETE CASCADE;
ALTER TABLE logement_unifie_saison_code_passerelle ADD CONSTRAINT FK_6B91F918CD3F9E86 FOREIGN KEY (logement_unifie_id) REFERENCES logement_unifie (id) ON DELETE CASCADE;
ALTER TABLE logement_unifie_saison_code_passerelle ADD CONSTRAINT FK_6B91F9182D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id) ON DELETE CASCADE;
ALTER TABLE fournisseur_prestation_annexe_saison_code_passerelle ADD CONSTRAINT FK_E64A62CCDF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id) ON DELETE CASCADE;
ALTER TABLE fournisseur_prestation_annexe_saison_code_passerelle ADD CONSTRAINT FK_E64A62CC2D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id) ON DELETE CASCADE;
ALTER TABLE saison_code_passerelle ADD CONSTRAINT FK_D3152958F965414C FOREIGN KEY (saison_id) REFERENCES saison (id);
ALTER TABLE code_passerelle ADD CONSTRAINT FK_EBA470652D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id);
ALTER TABLE param_passerelle ADD CONSTRAINT FK_6D47B385EBCA6F32 FOREIGN KEY (passerelle_id) REFERENCES pass (id);
ALTER TABLE arkiane ADD CONSTRAINT FK_D138B8D9BF396750 FOREIGN KEY (id) REFERENCES param_passerelle (id) ON DELETE CASCADE;
ALTER TABLE homeresa ADD CONSTRAINT FK_4FE7A6F5BF396750 FOREIGN KEY (id) REFERENCES param_passerelle (id) ON DELETE CASCADE;
ALTER TABLE pierre_vacances ADD CONSTRAINT FK_39E0C3C7BF396750 FOREIGN KEY (id) REFERENCES param_passerelle (id) ON DELETE CASCADE;
ALTER TABLE softbook ADD CONSTRAINT FK_25DA373C54C8C93 FOREIGN KEY (type_id) REFERENCES softbook_type (id);
ALTER TABLE softbook ADD CONSTRAINT FK_25DA373BF396750 FOREIGN KEY (id) REFERENCES param_passerelle (id) ON DELETE CASCADE;
ALTER TABLE anite ADD CONSTRAINT FK_8804F05ABF396750 FOREIGN KEY (id) REFERENCES param_passerelle (id) ON DELETE CASCADE;
DROP INDEX IDX_369ECA32EBCA6F32 ON fournisseur;
ALTER TABLE fournisseur ADD param_passerelle_id INT DEFAULT NULL, DROP passerelle_id;
ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA32A6EF357E FOREIGN KEY (param_passerelle_id) REFERENCES param_passerelle (id);
CREATE UNIQUE INDEX UNIQ_369ECA32A6EF357E ON fournisseur (param_passerelle_id);

