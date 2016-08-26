INSERT INTO `coordonnees_gps` (`id`, `latitude`, `longitude`, `precis`) VALUES (1, '48.856614', '2.3522219000000177', NULL);
INSERT INTO `coordonnees_gps` (`id`, `latitude`, `longitude`, `precis`) VALUES (2, '43.610769', '3.8767159999999876', NULL);

INSERT INTO `grande_ville` (`id`, `coordonnees_gps_id`) VALUES (1, 1);
INSERT INTO `grande_ville` (`id`, `coordonnees_gps_id`) VALUES (2, 2);

INSERT INTO `grande_ville_traduction` (`id`, `grande_ville_id`, `langue_id`, `libelle`) VALUES (1, 1, 1, 'Paris');
INSERT INTO `grande_ville_traduction` (`id`, `grande_ville_id`, `langue_id`, `libelle`) VALUES (2, 1, 2, 'Paris');
INSERT INTO `grande_ville_traduction` (`id`, `grande_ville_id`, `langue_id`, `libelle`) VALUES (4, 2, 1, 'Montpellier');
INSERT INTO `grande_ville_traduction` (`id`, `grande_ville_id`, `langue_id`, `libelle`) VALUES (5, 2, 2, 'Montpellier');
