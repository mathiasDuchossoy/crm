ALTER TABLE commande_ligne_prestation_annexe ADD station_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE commande_ligne_prestation_annexe ADD CONSTRAINT FK_E26A93A321BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
CREATE INDEX IDX_E26A93A321BDB235 ON commande_ligne_prestation_annexe (station_id);

ALTER TABLE commande_ligne ADD date_email_fournisseur DATETIME DEFAULT NULL;