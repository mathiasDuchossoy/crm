ALTER TABLE `tarif`
	CHANGE COLUMN `valeur` `valeur` DECIMAL(7,2) UNSIGNED NULL DEFAULT NULL AFTER `unite_id`;

ALTER TABLE `distance`
	CHANGE COLUMN `valeur` `valeur` DOUBLE UNSIGNED NOT NULL AFTER `unite_id`;

ALTER TABLE `description_forfait_ski`
	CHANGE COLUMN `quantite` `quantite` DOUBLE UNSIGNED NOT NULL AFTER `modele_id`;

ALTER TABLE `ligne_description_forfait_ski`
	CHANGE COLUMN `quantite` `quantite` DOUBLE UNSIGNED NOT NULL AFTER `present_id`;
