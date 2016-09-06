CREATE TABLE grande_ville_traduction (
  id              INT UNSIGNED AUTO_INCREMENT NOT NULL,
  grande_ville_id INT UNSIGNED DEFAULT NULL,
  langue_id       INT UNSIGNED DEFAULT NULL,
  libelle         VARCHAR(255)                NOT NULL,
  INDEX IDX_FC64968BAFDEF94D (grande_ville_id),
  INDEX IDX_FC64968B2AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE grande_ville (
  id                 INT UNSIGNED AUTO_INCREMENT NOT NULL,
  coordonnees_gps_id INT UNSIGNED DEFAULT NULL,
  UNIQUE INDEX UNIQ_DEC3C45D63A2FADA (coordonnees_gps_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;