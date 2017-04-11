/*CREATE TABLE fournisseur_hebergement_saison_code_passerelle (fournisseur_hebergement_id INT UNSIGNED NOT NULL, saison_code_passerelle_id INT NOT NULL, INDEX IDX_21C9F88D9E819CB8 (fournisseur_hebergement_id), INDEX IDX_21C9F88D2D75C1EB (saison_code_passerelle_id), PRIMARY KEY(fournisseur_hebergement_id, saison_code_passerelle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE saison_code_passerelle (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_hebergement_saison_code_passerelle ADD CONSTRAINT FK_21C9F88D9E819CB8 FOREIGN KEY (fournisseur_hebergement_id) REFERENCES fournisseur_hebergement (id) ON DELETE CASCADE;
ALTER TABLE fournisseur_hebergement_saison_code_passerelle ADD CONSTRAINT FK_21C9F88D2D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id) ON DELETE CASCADE;
ALTER TABLE code_passerelle ADD saison_code_passerelle_id INT DEFAULT NULL;
ALTER TABLE code_passerelle ADD CONSTRAINT FK_EBA470652D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id);
CREATE INDEX IDX_EBA470652D75C1EB ON code_passerelle (saison_code_passerelle_id);

ALTER TABLE saison_code_passerelle ADD saison_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE saison_code_passerelle ADD CONSTRAINT FK_D3152958F965414C FOREIGN KEY (saison_id) REFERENCES saison (id);
CREATE INDEX IDX_D3152958F965414C ON saison_code_passerelle (saison_id);*/

/*
CREATE TABLE logement_unifie_saison_code_passerelle (logement_unifie_id INT UNSIGNED NOT NULL, saison_code_passerelle_id INT NOT NULL, INDEX IDX_6B91F918CD3F9E86 (logement_unifie_id), INDEX IDX_6B91F9182D75C1EB (saison_code_passerelle_id), PRIMARY KEY(logement_unifie_id, saison_code_passerelle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE logement_unifie_saison_code_passerelle ADD CONSTRAINT FK_6B91F918CD3F9E86 FOREIGN KEY (logement_unifie_id) REFERENCES logement_unifie (id) ON DELETE CASCADE;
ALTER TABLE logement_unifie_saison_code_passerelle ADD CONSTRAINT FK_6B91F9182D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id) ON DELETE CASCADE;
*/

CREATE TABLE fournisseur_prestation_annexe_saison_code_passerelle (fournisseur_prestation_annexe_id INT UNSIGNED NOT NULL, saison_code_passerelle_id INT NOT NULL, INDEX IDX_E64A62CCDF2F2EF6 (fournisseur_prestation_annexe_id), INDEX IDX_E64A62CC2D75C1EB (saison_code_passerelle_id), PRIMARY KEY(fournisseur_prestation_annexe_id, saison_code_passerelle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_prestation_annexe_saison_code_passerelle ADD CONSTRAINT FK_E64A62CCDF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id) ON DELETE CASCADE;
ALTER TABLE fournisseur_prestation_annexe_saison_code_passerelle ADD CONSTRAINT FK_E64A62CC2D75C1EB FOREIGN KEY (saison_code_passerelle_id) REFERENCES saison_code_passerelle (id) ON DELETE CASCADE;
