CREATE TABLE condition_annulation_description (id INT UNSIGNED AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, standard TINYINT(1) DEFAULT '0' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur ADD condition_annulation_description_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA32A87B6C12 FOREIGN KEY (condition_annulation_description_id) REFERENCES condition_annulation_description (id);
CREATE INDEX IDX_369ECA32A87B6C12 ON fournisseur (condition_annulation_description_id);