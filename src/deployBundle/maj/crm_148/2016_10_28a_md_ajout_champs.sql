/* ALTER TABLE hebergement_traduction ADD accroche LONGTEXT NOT NULL, ADD generalite LONGTEXT NOT NULL, ADD avis_hebergement LONGTEXT NOT NULL, ADD avis_logement LONGTEXT NOT NULL; */
ALTER TABLE hebergement_traduction ADD accroche LONGTEXT DEFAULT NULL, ADD generalite LONGTEXT DEFAULT NULL, ADD avis_hebergement LONGTEXT DEFAULT NULL, ADD avis_logement LONGTEXT DEFAULT NULL, CHANGE restauration restauration LONGTEXT DEFAULT NULL, CHANGE bien_etre bien_etre LONGTEXT DEFAULT NULL, CHANGE pour_les_enfants pour_les_enfants LONGTEXT DEFAULT NULL, CHANGE activites activites LONGTEXT DEFAULT NULL;

