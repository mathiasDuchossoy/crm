CREATE TABLE mot_clef_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, langue_id INT UNSIGNED DEFAULT NULL, mot_clef_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_208C3AB12AADBACD (langue_id), INDEX IDX_208C3AB1E2959304 (mot_clef_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE mot_clef_traduction_hebergement (mot_clef_traduction_id INT UNSIGNED NOT NULL, hebergement_id INT UNSIGNED NOT NULL, classement SMALLINT NOT NULL, INDEX IDX_4DA9025D637A4045 (mot_clef_traduction_id), INDEX IDX_4DA9025D23BB0F66 (hebergement_id), PRIMARY KEY(mot_clef_traduction_id, hebergement_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE mot_clef_traduction ADD CONSTRAINT FK_208C3AB12AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE mot_clef_traduction ADD CONSTRAINT FK_208C3AB1E2959304 FOREIGN KEY (mot_clef_id) REFERENCES mot_clef (id);
ALTER TABLE mot_clef_traduction_hebergement ADD CONSTRAINT FK_4DA9025D637A4045 FOREIGN KEY (mot_clef_traduction_id) REFERENCES mot_clef_traduction (id);
ALTER TABLE mot_clef_traduction_hebergement ADD CONSTRAINT FK_4DA9025D23BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE mot_clef DROP FOREIGN KEY FK_ADC770E42AADBACD;
DROP INDEX IDX_ADC770E42AADBACD ON mot_clef;
ALTER TABLE mot_clef DROP langue_id, DROP libelle;