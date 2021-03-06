CREATE TABLE commentaire_client (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date_heure_creation DATETIME NOT NULL, date_heure_modification DATETIME NOT NULL, utilisateur_modification VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, validation_moderateur TINYINT(1) DEFAULT '1' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commentaire_interne (id INT UNSIGNED AUTO_INCREMENT NOT NULL, auteur_id INT UNSIGNED DEFAULT NULL, date_heure_creation DATETIME NOT NULL, date_heure_modification DATETIME NOT NULL, validation_moderateur TINYINT(1) NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_530D04CF60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commentaire_utilisateur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, utilisateur_id INT UNSIGNED DEFAULT NULL, commentaire_parent_id INT UNSIGNED DEFAULT NULL, date_heure_creation DATETIME NOT NULL, date_heure_modification DATETIME NOT NULL, validation_moderateur TINYINT(1) NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_F01CD2A1FB88E14F (utilisateur_id), INDEX IDX_F01CD2A1FDED4547 (commentaire_parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_commentaire_interne (commande_id INT UNSIGNED NOT NULL, commentaire_interne_id INT UNSIGNED NOT NULL, INDEX IDX_1E4F913B82EA2E54 (commande_id), INDEX IDX_1E4F913BE66FADA7 (commentaire_interne_id), PRIMARY KEY(commande_id, commentaire_interne_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE commentaire_interne ADD CONSTRAINT FK_530D04CF60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id);
ALTER TABLE commentaire_utilisateur ADD CONSTRAINT FK_F01CD2A1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id);
ALTER TABLE commentaire_utilisateur ADD CONSTRAINT FK_F01CD2A1FDED4547 FOREIGN KEY (commentaire_parent_id) REFERENCES commentaire_client (id);
ALTER TABLE commande_commentaire_interne ADD CONSTRAINT FK_1E4F913B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE;
ALTER TABLE commande_commentaire_interne ADD CONSTRAINT FK_1E4F913BE66FADA7 FOREIGN KEY (commentaire_interne_id) REFERENCES commentaire_interne (id) ON DELETE CASCADE;
ALTER TABLE commande ADD commentaire_client_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D967CE616 FOREIGN KEY (commentaire_client_id) REFERENCES commentaire_client (id);
CREATE INDEX IDX_6EEAA67D967CE616 ON commande (commentaire_client_id);

ALTER TABLE utilisateur_auteur DROP FOREIGN KEY FK_64F78BCBBF396750;
ALTER TABLE utilisateur_auteur CHANGE id id INT NOT NULL;