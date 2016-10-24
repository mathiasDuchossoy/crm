CREATE TABLE station_station_label (station_id INT UNSIGNED NOT NULL, station_label_id INT UNSIGNED NOT NULL, INDEX IDX_CF29A62921BDB235 (station_id), INDEX IDX_CF29A629BF23F56F (station_label_id), PRIMARY KEY(station_id, station_label_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE station_label (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE station_label_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, station_label_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_497B0491BF23F56F (station_label_id), INDEX IDX_497B04912AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE station_station_label ADD CONSTRAINT FK_CF29A62921BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON DELETE CASCADE;
ALTER TABLE station_station_label ADD CONSTRAINT FK_CF29A629BF23F56F FOREIGN KEY (station_label_id) REFERENCES station_label (id) ON DELETE CASCADE;
ALTER TABLE station_label_traduction ADD CONSTRAINT FK_497B0491BF23F56F FOREIGN KEY (station_label_id) REFERENCES station_label (id);
ALTER TABLE station_label_traduction ADD CONSTRAINT FK_497B04912AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);

INSERT INTO `station_label` (`id`) VALUES (1);
INSERT INTO `station_label` (`id`) VALUES (2);
INSERT INTO `station_label` (`id`) VALUES (3);
INSERT INTO `station_label` (`id`) VALUES (4);
INSERT INTO `station_label` (`id`) VALUES (5);
INSERT INTO `station_label` (`id`) VALUES (6);

INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (1, 1, 1, 'Famille Plus');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (2, 1, 2, 'Famille Plus');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (3, 2, 1, 'Famille Plus Montagne');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (4, 2, 2, 'Famille Plus Montagne');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (5, 3, 1, 'Plus Beau Village de France');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (6, 3, 2, 'Plus Beau Village de France');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (7, 4, 1, 'Site Patrimoine Mondial de l\'Unesco');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (8, 4, 2, 'Site Patrimoine Mondial de l\'Unesco');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (9, 5, 1, 'Station Verte');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (10, 5, 2, 'Station Verte');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (11, 6, 1, 'Plage Pavillon Bleu');
INSERT INTO `station_label_traduction` (`id`, `station_label_id`, `langue_id`, `libelle`) VALUES (12, 6, 2, 'Plage Pavillon Bleu');
