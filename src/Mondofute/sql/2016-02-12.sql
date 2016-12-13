-- GESTION DU BUNDLE UNITE
CREATE TABLE age (
  id       INT AUTO_INCREMENT NOT NULL,
  unite_id INT DEFAULT NULL,
  valeur   INT                NOT NULL,
  INDEX IDX_A13010B2EC4A74AB (unite_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE distance (
  id       INT AUTO_INCREMENT NOT NULL,
  unite_id INT DEFAULT NULL,
  valeur   DOUBLE PRECISION   NOT NULL,
  INDEX IDX_1C929A81EC4A74AB (unite_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE tarif (
  id       INT AUTO_INCREMENT NOT NULL,
  unite_id INT DEFAULT NULL,
  valeur   NUMERIC(7, 2)      NOT NULL,
  INDEX IDX_E7189C9EC4A74AB (unite_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE unite (
  id                       INT AUTO_INCREMENT NOT NULL,
  reference_id             INT DEFAULT NULL,
  multiplicateur_reference DOUBLE PRECISION   NOT NULL,
  discr                    INT                NOT NULL,
  INDEX IDX_1D64C1181645DEA9 (reference_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE unite_traduction (
  id            INT AUTO_INCREMENT NOT NULL,
  langue_id     INT UNSIGNED DEFAULT NULL,
  unite_id      INT          DEFAULT NULL,
  libelle       VARCHAR(255)       NOT NULL,
  libelle_court VARCHAR(10)        NOT NULL,
  INDEX IDX_C9BE47932AADBACD (langue_id),
  INDEX IDX_C9BE4793EC4A74AB (unite_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE age
  ADD CONSTRAINT FK_A13010B2EC4A74AB FOREIGN KEY (unite_id) REFERENCES unite (id);
ALTER TABLE distance
  ADD CONSTRAINT FK_1C929A81EC4A74AB FOREIGN KEY (unite_id) REFERENCES unite (id);
ALTER TABLE tarif
  ADD CONSTRAINT FK_E7189C9EC4A74AB FOREIGN KEY (unite_id) REFERENCES unite (id);
ALTER TABLE unite
  ADD CONSTRAINT FK_1D64C1181645DEA9 FOREIGN KEY (reference_id) REFERENCES unite (id);
ALTER TABLE unite_traduction
  ADD CONSTRAINT FK_C9BE47932AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE unite_traduction
  ADD CONSTRAINT FK_C9BE4793EC4A74AB FOREIGN KEY (unite_id) REFERENCES unite (id);

-- GESTION DU BUNDLE DESCRIPTIONFORFAITSKI

-- gestion des categories

CREATE TABLE ligne_description_forfait_ski_categorie (
  id         INT AUTO_INCREMENT NOT NULL,
  classement INT                NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE ligne_description_forfait_ski_categorie_traduction (
  id                                         INT AUTO_INCREMENT NOT NULL,
  ligne_description_forfait_ski_categorie_id INT DEFAULT NULL,
  libelle                                    VARCHAR(255)       NOT NULL,
  INDEX IDX_584F33B151FCD9FD (ligne_description_forfait_ski_categorie_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE ligne_description_forfait_ski_categorie_traduction
  ADD CONSTRAINT FK_584F33B151FCD9FD FOREIGN KEY (ligne_description_forfait_ski_categorie_id) REFERENCES ligne_description_forfait_ski_categorie (id);
ALTER TABLE ligne_description_forfait_ski_categorie_traduction
  ADD langue_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE ligne_description_forfait_ski_categorie_traduction
  ADD CONSTRAINT FK_584F33B12AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
CREATE INDEX IDX_584F33B12AADBACD
  ON ligne_description_forfait_ski_categorie_traduction (langue_id);

-- gestion des lignes

CREATE TABLE ligne_description_forfait_ski (
  id           INT AUTO_INCREMENT NOT NULL,
  categorie_id SMALLINT UNSIGNED DEFAULT NULL,
  quantite     DOUBLE PRECISION   NOT NULL,
  INDEX IDX_DBEC9BFABCF5E72D (categorie_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE ligne_description_forfait_ski_traduction (
  id                               INT AUTO_INCREMENT NOT NULL,
  ligne_description_forfait_ski_id SMALLINT UNSIGNED DEFAULT NULL,
  langue_id                        INT UNSIGNED      DEFAULT NULL,
  texte_dur                        VARCHAR(255)       NOT NULL,
  description                      LONGTEXT           NOT NULL,
  INDEX IDX_7995A793E51BCE6F (ligne_description_forfait_ski_id),
  INDEX IDX_7995A7932AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE ligne_description_forfait_ski
  ADD CONSTRAINT FK_DBEC9BFABCF5E72D FOREIGN KEY (categorie_id) REFERENCES ligne_description_forfait_ski_categorie (id);
ALTER TABLE ligne_description_forfait_ski_traduction
  ADD CONSTRAINT FK_7995A793E51BCE6F FOREIGN KEY (ligne_description_forfait_ski_id) REFERENCES ligne_description_forfait_ski (id);
ALTER TABLE ligne_description_forfait_ski_traduction
  ADD CONSTRAINT FK_7995A7932AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);


CREATE TABLE oui_non_nc (
  id         SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  classement SMALLINT UNSIGNED                NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE oui_non_nctraduction (
  id            SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  langue_id     INT UNSIGNED      DEFAULT NULL,
  oui_non_nc_id SMALLINT UNSIGNED DEFAULT NULL,
  libelle       VARCHAR(255)                     NOT NULL,
  INDEX IDX_CBCDC9922AADBACD (langue_id),
  INDEX IDX_CBCDC992D432222D (oui_non_nc_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE oui_non_nctraduction
  ADD CONSTRAINT FK_CBCDC9922AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE oui_non_nctraduction
  ADD CONSTRAINT FK_CBCDC992D432222D FOREIGN KEY (oui_non_nc_id) REFERENCES oui_non_nc (id);
ALTER TABLE ligne_description_forfait_ski
  ADD prix_id INT DEFAULT NULL,
  ADD age_min_id INT DEFAULT NULL,
  ADD age_max_id INT DEFAULT NULL,
  ADD present_id SMALLINT UNSIGNED DEFAULT NULL,
  CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  CHANGE categorie_id categorie_id SMALLINT UNSIGNED DEFAULT NULL;
ALTER TABLE ligne_description_forfait_ski
  ADD CONSTRAINT FK_DBEC9BFA944722F2 FOREIGN KEY (prix_id) REFERENCES tarif (id);
ALTER TABLE ligne_description_forfait_ski
  ADD CONSTRAINT FK_DBEC9BFACA8A497E FOREIGN KEY (age_min_id) REFERENCES age (id);
ALTER TABLE ligne_description_forfait_ski
  ADD CONSTRAINT FK_DBEC9BFA8F880AFC FOREIGN KEY (age_max_id) REFERENCES age (id);
ALTER TABLE ligne_description_forfait_ski
  ADD CONSTRAINT FK_DBEC9BFA8D7B1EF8 FOREIGN KEY (present_id) REFERENCES oui_non_nc (id);
CREATE UNIQUE INDEX UNIQ_DBEC9BFA944722F2
  ON ligne_description_forfait_ski (prix_id);
CREATE UNIQUE INDEX UNIQ_DBEC9BFACA8A497E
  ON ligne_description_forfait_ski (age_min_id);
CREATE UNIQUE INDEX UNIQ_DBEC9BFA8F880AFC
  ON ligne_description_forfait_ski (age_max_id);
CREATE UNIQUE INDEX UNIQ_DBEC9BFA8D7B1EF8
  ON ligne_description_forfait_ski (present_id);
ALTER TABLE ligne_description_forfait_ski_categorie
  CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE ligne_description_forfait_ski_categorie_traduction
  CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  CHANGE ligne_description_forfait_ski_categorie_id ligne_description_forfait_ski_categorie_id SMALLINT UNSIGNED DEFAULT NULL;
ALTER TABLE ligne_description_forfait_ski_traduction
  CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  CHANGE ligne_description_forfait_ski_id ligne_description_forfait_ski_id SMALLINT UNSIGNED DEFAULT NULL;