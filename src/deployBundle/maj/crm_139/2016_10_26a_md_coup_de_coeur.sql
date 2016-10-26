CREATE TABLE hebergement_coup_de_coeur (id INT AUTO_INCREMENT NOT NULL, DateHeureDebut DATETIME NOT NULL, DateHeureFin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE hebergement ADD coup_de_coeur_id INT DEFAULT NULL;
ALTER TABLE hebergement ADD CONSTRAINT FK_4852DD9C18DC2A91 FOREIGN KEY (coup_de_coeur_id) REFERENCES hebergement_coup_de_coeur (id);
CREATE UNIQUE INDEX UNIQ_4852DD9C18DC2A91 ON hebergement (coup_de_coeur_id);