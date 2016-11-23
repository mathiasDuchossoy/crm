
CREATE TABLE nombre_de_chambre (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE nombre_de_chambre_traduction (id INT AUTO_INCREMENT NOT NULL, nombre_de_chambre_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_D18D499F17036B1E (nombre_de_chambre_id), INDEX IDX_D18D499F2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE nombre_de_chambre_traduction ADD CONSTRAINT FK_D18D499F17036B1E FOREIGN KEY (nombre_de_chambre_id) REFERENCES nombre_de_chambre (id);
ALTER TABLE nombre_de_chambre_traduction ADD CONSTRAINT FK_D18D499F2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE logement ADD nombre_de_chambre_id INT UNSIGNED DEFAULT NULL, DROP nb_chambre;
ALTER TABLE logement ADD CONSTRAINT FK_F0FD445717036B1E FOREIGN KEY (nombre_de_chambre_id) REFERENCES nombre_de_chambre (id);
CREATE INDEX IDX_F0FD445717036B1E ON logement (nombre_de_chambre_id);
