CREATE TABLE station_photo (
  id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_video (
  id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_visuel (
  id         INT UNSIGNED AUTO_INCREMENT NOT NULL,
  station_id INT UNSIGNED DEFAULT NULL,
  visuel_id  INT          DEFAULT NULL,
  actif      TINYINT(1) DEFAULT '0'      NOT NULL,
  discr      INT                         NOT NULL,
  INDEX IDX_40475EFD21BDB235 (station_id),
  INDEX IDX_40475EFD9559EF01 (visuel_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE station_visuel_traduction (
  id                INT UNSIGNED AUTO_INCREMENT NOT NULL,
  station_visuel_id INT UNSIGNED DEFAULT NULL,
  langue_id         INT UNSIGNED DEFAULT NULL,
  libelle           VARCHAR(255)                NOT NULL,
  INDEX IDX_DA23C36317DD811A (station_visuel_id),
  INDEX IDX_DA23C3632AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE station_photo
  ADD CONSTRAINT FK_4C31FB2EBF396750 FOREIGN KEY (id) REFERENCES station_visuel (id)
  ON DELETE CASCADE;
ALTER TABLE station_video
  ADD CONSTRAINT FK_2441A51ABF396750 FOREIGN KEY (id) REFERENCES station_visuel (id)
  ON DELETE CASCADE;
ALTER TABLE station_visuel
  ADD CONSTRAINT FK_40475EFD21BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
ALTER TABLE station_visuel
  ADD CONSTRAINT FK_40475EFD9559EF01 FOREIGN KEY (visuel_id) REFERENCES media__media (id);
ALTER TABLE station_visuel_traduction
  ADD CONSTRAINT FK_DA23C36317DD811A FOREIGN KEY (station_visuel_id) REFERENCES station_visuel (id);
ALTER TABLE station_visuel_traduction
  ADD CONSTRAINT FK_DA23C3632AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
