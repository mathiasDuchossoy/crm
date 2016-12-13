USE `crm2`;
ALTER TABLE `profil_traduction`
  CHANGE accueil accueil VARCHAR(255) DEFAULT '' NOT NULL;

USE `skifute2`;
ALTER TABLE `profil_traduction`
  CHANGE accueil accueil VARCHAR(255) DEFAULT '' NOT NULL;

USE `francefute2`;
ALTER TABLE `profil_traduction`
  CHANGE accueil accueil VARCHAR(255) DEFAULT '' NOT NULL;