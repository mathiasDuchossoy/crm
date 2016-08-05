CREATE TABLE IF NOT EXISTS secteur_photo (
  id         INT UNSIGNED AUTO_INCREMENT NOT NULL,
  secteur_id INT UNSIGNED DEFAULT NULL,
  photo_id   INT          DEFAULT NULL,
  actif      TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_28D1CB319F7E4405 (secteur_id),
  INDEX IDX_28D1CB317E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS departement_photo (
  id             INT UNSIGNED AUTO_INCREMENT NOT NULL,
  departement_id INT UNSIGNED DEFAULT NULL,
  photo_id       INT          DEFAULT NULL,
  actif          TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_8115E89DCCF9E01E (departement_id),
  INDEX IDX_8115E89D7E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS zone_touristique_image_traduction (
  id        INT AUTO_INCREMENT NOT NULL,
  image_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)       NOT NULL,
  INDEX IDX_D04998263DA5256D (image_id),
  INDEX IDX_D04998262AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS zone_touristique_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_F0F361187E9E4C8C (photo_id),
  INDEX IDX_F0F361182AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS secteur_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_EC93BEC37E9E4C8C (photo_id),
  INDEX IDX_EC93BEC32AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS departement_image (
  id             INT UNSIGNED AUTO_INCREMENT NOT NULL,
  departement_id INT UNSIGNED DEFAULT NULL,
  image_id       INT          DEFAULT NULL,
  actif          TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_509F68DACCF9E01E (departement_id),
  INDEX IDX_509F68DA3DA5256D (image_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS region_photo (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  region_id INT UNSIGNED DEFAULT NULL,
  photo_id  INT          DEFAULT NULL,
  actif     TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_ACD3870098260155 (region_id),
  INDEX IDX_ACD387007E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS profil_photo (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  profil_id INT UNSIGNED DEFAULT NULL,
  photo_id  INT          DEFAULT NULL,
  actif     TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_5ABB5F5C275ED078 (profil_id),
  INDEX IDX_5ABB5F5C7E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS profil_image_traduction (
  id        INT AUTO_INCREMENT NOT NULL,
  image_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)       NOT NULL,
  INDEX IDX_B81129863DA5256D (image_id),
  INDEX IDX_B81129862AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS profil_image (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  profil_id INT UNSIGNED DEFAULT NULL,
  image_id  INT          DEFAULT NULL,
  actif     TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_8B31DF1B275ED078 (profil_id),
  INDEX IDX_8B31DF1B3DA5256D (image_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS departement_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_E3B7807C7E9E4C8C (photo_id),
  INDEX IDX_E3B7807C2AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS zone_touristique_photo (
  id                  INT UNSIGNED AUTO_INCREMENT NOT NULL,
  zone_touristique_id INT UNSIGNED DEFAULT NULL,
  photo_id            INT          DEFAULT NULL,
  actif               TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_2856197E1314056E (zone_touristique_id),
  INDEX IDX_2856197E7E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS region_image (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  region_id INT UNSIGNED DEFAULT NULL,
  image_id  INT          DEFAULT NULL,
  actif     TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_7D59074798260155 (region_id),
  INDEX IDX_7D5907473DA5256D (image_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS region_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_78766E977E9E4C8C (photo_id),
  INDEX IDX_78766E972AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS profil_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_98ABD0B87E9E4C8C (photo_id),
  INDEX IDX_98ABD0B82AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS departement_image_traduction (
  id        INT AUTO_INCREMENT NOT NULL,
  image_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)       NOT NULL,
  INDEX IDX_C30D79423DA5256D (image_id),
  INDEX IDX_C30D79422AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS region_image_traduction (
  id        INT AUTO_INCREMENT NOT NULL,
  image_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)       NOT NULL,
  INDEX IDX_58CC97A93DA5256D (image_id),
  INDEX IDX_58CC97A92AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS secteur_image (
  id         INT UNSIGNED AUTO_INCREMENT NOT NULL,
  secteur_id INT UNSIGNED DEFAULT NULL,
  image_id   INT          DEFAULT NULL,
  actif      TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_F95B4B769F7E4405 (secteur_id),
  INDEX IDX_F95B4B763DA5256D (image_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS secteur_image_traduction (
  id        INT AUTO_INCREMENT NOT NULL,
  image_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)       NOT NULL,
  INDEX IDX_CC2947FD3DA5256D (image_id),
  INDEX IDX_CC2947FD2AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS zone_touristique_image (
  id                  INT UNSIGNED AUTO_INCREMENT NOT NULL,
  zone_touristique_id INT UNSIGNED DEFAULT NULL,
  image_id            INT          DEFAULT NULL,
  actif               TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_F9DC99391314056E (zone_touristique_id),
  INDEX IDX_F9DC99393DA5256D (image_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS domaine_carte_identite_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_A8F2014D7E9E4C8C (photo_id),
  INDEX IDX_A8F2014D2AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS domaine_carte_identite_photo (
  id                        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  domaine_carte_identite_id INT UNSIGNED DEFAULT NULL,
  photo_id                  INT          DEFAULT NULL,
  actif                     TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_C488EDB8A8EF6DB (domaine_carte_identite_id),
  INDEX IDX_C488EDB87E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS domaine_carte_identite_image_traduction (
  id        INT AUTO_INCREMENT NOT NULL,
  image_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)       NOT NULL,
  INDEX IDX_8848F8733DA5256D (image_id),
  INDEX IDX_8848F8732AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS domaine_carte_identite_image (
  id                        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  domaine_carte_identite_id INT UNSIGNED DEFAULT NULL,
  image_id                  INT          DEFAULT NULL,
  actif                     TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_15026DFFA8EF6DB (domaine_carte_identite_id),
  INDEX IDX_15026DFF3DA5256D (image_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS hebergement_visuel_traduction (
  id                    INT UNSIGNED AUTO_INCREMENT NOT NULL,
  hebergement_visuel_id INT UNSIGNED DEFAULT NULL,
  langue_id             INT UNSIGNED DEFAULT NULL,
  libelle               VARCHAR(255)                NOT NULL,
  INDEX IDX_9646D76786FD4FB0 (hebergement_visuel_id),
  INDEX IDX_9646D7672AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS hebergement_photo (
  id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS hebergement_video (
  id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS hebergement_visuel (
  id             INT UNSIGNED AUTO_INCREMENT NOT NULL,
  hebergement_id INT UNSIGNED DEFAULT NULL,
  visuel_id      INT          DEFAULT NULL,
  actif          TINYINT(1) DEFAULT '0'      NOT NULL,
  discr          INT                         NOT NULL,
  INDEX IDX_EC2D555823BB0F66 (hebergement_id),
  INDEX IDX_EC2D55589559EF01 (visuel_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS logement_photo (
  id          INT UNSIGNED AUTO_INCREMENT NOT NULL,
  logement_id INT UNSIGNED DEFAULT NULL,
  photo_id    INT          DEFAULT NULL,
  actif       TINYINT(1) DEFAULT '0'      NOT NULL,
  INDEX IDX_E3BF13E858ABF955 (logement_id),
  INDEX IDX_E3BF13E87E9E4C8C (photo_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS logement_photo_traduction (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  photo_id  INT UNSIGNED DEFAULT NULL,
  langue_id INT UNSIGNED DEFAULT NULL,
  libelle   VARCHAR(255)                NOT NULL,
  INDEX IDX_672AA6177E9E4C8C (photo_id),
  INDEX IDX_672AA6172AADBACD (langue_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS media__gallery_media (
  id         INT AUTO_INCREMENT NOT NULL,
  gallery_id INT DEFAULT NULL,
  media_id   INT DEFAULT NULL,
  position   INT                NOT NULL,
  enabled    TINYINT(1)         NOT NULL,
  updated_at DATETIME           NOT NULL,
  created_at DATETIME           NOT NULL,
  INDEX IDX_80D4C5414E7AF8F (gallery_id),
  INDEX IDX_80D4C541EA9FDD75 (media_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS media__gallery (
  id             INT AUTO_INCREMENT NOT NULL,
  name           VARCHAR(255)       NOT NULL,
  context        VARCHAR(64)        NOT NULL,
  default_format VARCHAR(255)       NOT NULL,
  enabled        TINYINT(1)         NOT NULL,
  updated_at     DATETIME           NOT NULL,
  created_at     DATETIME           NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS media__media (
  id                 INT AUTO_INCREMENT NOT NULL,
  name               VARCHAR(255)       NOT NULL,
  description        TEXT           DEFAULT NULL,
  enabled            TINYINT(1)         NOT NULL,
  provider_name      VARCHAR(255)       NOT NULL,
  provider_status    INT                NOT NULL,
  provider_reference VARCHAR(255)       NOT NULL,
  provider_metadata  LONGTEXT       DEFAULT NULL
  COMMENT '(DC2Type:json)',
  width              INT            DEFAULT NULL,
  height             INT            DEFAULT NULL,
  length             NUMERIC(10, 0) DEFAULT NULL,
  content_type       VARCHAR(255)   DEFAULT NULL,
  content_size       INT            DEFAULT NULL,
  copyright          VARCHAR(255)   DEFAULT NULL,
  author_name        VARCHAR(255)   DEFAULT NULL,
  context            VARCHAR(64)    DEFAULT NULL,
  cdn_is_flushable   TINYINT(1)     DEFAULT NULL,
  cdn_flush_at       DATETIME       DEFAULT NULL,
  cdn_status         INT            DEFAULT NULL,
  updated_at         DATETIME           NOT NULL,
  created_at         DATETIME           NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE secteur_photo
  ADD CONSTRAINT FK_28D1CB319F7E4405 FOREIGN KEY (secteur_id) REFERENCES secteur (id);
ALTER TABLE secteur_photo
  ADD CONSTRAINT FK_28D1CB317E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE departement_photo
  ADD CONSTRAINT FK_8115E89DCCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id);
ALTER TABLE departement_photo
  ADD CONSTRAINT FK_8115E89D7E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE zone_touristique_image_traduction
  ADD CONSTRAINT FK_D04998263DA5256D FOREIGN KEY (image_id) REFERENCES zone_touristique_image (id);
ALTER TABLE zone_touristique_image_traduction
  ADD CONSTRAINT FK_D04998262AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE zone_touristique_photo_traduction
  ADD CONSTRAINT FK_F0F361187E9E4C8C FOREIGN KEY (photo_id) REFERENCES zone_touristique_photo (id);
ALTER TABLE zone_touristique_photo_traduction
  ADD CONSTRAINT FK_F0F361182AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE secteur_photo_traduction
  ADD CONSTRAINT FK_EC93BEC37E9E4C8C FOREIGN KEY (photo_id) REFERENCES secteur_photo (id);
ALTER TABLE secteur_photo_traduction
  ADD CONSTRAINT FK_EC93BEC32AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE departement_image
  ADD CONSTRAINT FK_509F68DACCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id);
ALTER TABLE departement_image
  ADD CONSTRAINT FK_509F68DA3DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id);
ALTER TABLE region_photo
  ADD CONSTRAINT FK_ACD3870098260155 FOREIGN KEY (region_id) REFERENCES region (id);
ALTER TABLE region_photo
  ADD CONSTRAINT FK_ACD387007E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE profil_photo
  ADD CONSTRAINT FK_5ABB5F5C275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id);
ALTER TABLE profil_photo
  ADD CONSTRAINT FK_5ABB5F5C7E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE profil_image_traduction
  ADD CONSTRAINT FK_B81129863DA5256D FOREIGN KEY (image_id) REFERENCES profil_image (id);
ALTER TABLE profil_image_traduction
  ADD CONSTRAINT FK_B81129862AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE profil_image
  ADD CONSTRAINT FK_8B31DF1B275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id);
ALTER TABLE profil_image
  ADD CONSTRAINT FK_8B31DF1B3DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id);
ALTER TABLE departement_photo_traduction
  ADD CONSTRAINT FK_E3B7807C7E9E4C8C FOREIGN KEY (photo_id) REFERENCES departement_photo (id);
ALTER TABLE departement_photo_traduction
  ADD CONSTRAINT FK_E3B7807C2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE zone_touristique_photo
  ADD CONSTRAINT FK_2856197E1314056E FOREIGN KEY (zone_touristique_id) REFERENCES zone_touristique (id);
ALTER TABLE zone_touristique_photo
  ADD CONSTRAINT FK_2856197E7E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE region_image
  ADD CONSTRAINT FK_7D59074798260155 FOREIGN KEY (region_id) REFERENCES region (id);
ALTER TABLE region_image
  ADD CONSTRAINT FK_7D5907473DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id);
ALTER TABLE region_photo_traduction
  ADD CONSTRAINT FK_78766E977E9E4C8C FOREIGN KEY (photo_id) REFERENCES region_photo (id);
ALTER TABLE region_photo_traduction
  ADD CONSTRAINT FK_78766E972AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE profil_photo_traduction
  ADD CONSTRAINT FK_98ABD0B87E9E4C8C FOREIGN KEY (photo_id) REFERENCES profil_photo (id);
ALTER TABLE profil_photo_traduction
  ADD CONSTRAINT FK_98ABD0B82AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE departement_image_traduction
  ADD CONSTRAINT FK_C30D79423DA5256D FOREIGN KEY (image_id) REFERENCES departement_image (id);
ALTER TABLE departement_image_traduction
  ADD CONSTRAINT FK_C30D79422AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE region_image_traduction
  ADD CONSTRAINT FK_58CC97A93DA5256D FOREIGN KEY (image_id) REFERENCES region_image (id);
ALTER TABLE region_image_traduction
  ADD CONSTRAINT FK_58CC97A92AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE secteur_image
  ADD CONSTRAINT FK_F95B4B769F7E4405 FOREIGN KEY (secteur_id) REFERENCES secteur (id);
ALTER TABLE secteur_image
  ADD CONSTRAINT FK_F95B4B763DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id);
ALTER TABLE secteur_image_traduction
  ADD CONSTRAINT FK_CC2947FD3DA5256D FOREIGN KEY (image_id) REFERENCES secteur_image (id);
ALTER TABLE secteur_image_traduction
  ADD CONSTRAINT FK_CC2947FD2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE zone_touristique_image
  ADD CONSTRAINT FK_F9DC99391314056E FOREIGN KEY (zone_touristique_id) REFERENCES zone_touristique (id);
ALTER TABLE zone_touristique_image
  ADD CONSTRAINT FK_F9DC99393DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id);
ALTER TABLE domaine_carte_identite_photo_traduction
  ADD CONSTRAINT FK_A8F2014D7E9E4C8C FOREIGN KEY (photo_id) REFERENCES domaine_carte_identite_photo (id);
ALTER TABLE domaine_carte_identite_photo_traduction
  ADD CONSTRAINT FK_A8F2014D2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE domaine_carte_identite_photo
  ADD CONSTRAINT FK_C488EDB8A8EF6DB FOREIGN KEY (domaine_carte_identite_id) REFERENCES domaine_carte_identite (id);
ALTER TABLE domaine_carte_identite_photo
  ADD CONSTRAINT FK_C488EDB87E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE domaine_carte_identite_image_traduction
  ADD CONSTRAINT FK_8848F8733DA5256D FOREIGN KEY (image_id) REFERENCES domaine_carte_identite_image (id);
ALTER TABLE domaine_carte_identite_image_traduction
  ADD CONSTRAINT FK_8848F8732AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE domaine_carte_identite_image
  ADD CONSTRAINT FK_15026DFFA8EF6DB FOREIGN KEY (domaine_carte_identite_id) REFERENCES domaine_carte_identite (id);
ALTER TABLE domaine_carte_identite_image
  ADD CONSTRAINT FK_15026DFF3DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id);
ALTER TABLE hebergement_visuel_traduction
  ADD CONSTRAINT FK_9646D76786FD4FB0 FOREIGN KEY (hebergement_visuel_id) REFERENCES hebergement_visuel (id);
ALTER TABLE hebergement_visuel_traduction
  ADD CONSTRAINT FK_9646D7672AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE hebergement_photo
  ADD CONSTRAINT FK_9AC31E05BF396750 FOREIGN KEY (id) REFERENCES hebergement_visuel (id)
  ON DELETE CASCADE;
ALTER TABLE hebergement_video
  ADD CONSTRAINT FK_F2B34031BF396750 FOREIGN KEY (id) REFERENCES hebergement_visuel (id)
  ON DELETE CASCADE;
ALTER TABLE hebergement_visuel
  ADD CONSTRAINT FK_EC2D555823BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);
ALTER TABLE hebergement_visuel
  ADD CONSTRAINT FK_EC2D55589559EF01 FOREIGN KEY (visuel_id) REFERENCES media__media (id);
ALTER TABLE logement_photo
  ADD CONSTRAINT FK_E3BF13E858ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE logement_photo
  ADD CONSTRAINT FK_E3BF13E87E9E4C8C FOREIGN KEY (photo_id) REFERENCES media__media (id);
ALTER TABLE logement_photo_traduction
  ADD CONSTRAINT FK_672AA6177E9E4C8C FOREIGN KEY (photo_id) REFERENCES logement_photo (id);
ALTER TABLE logement_photo_traduction
  ADD CONSTRAINT FK_672AA6172AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE media__gallery_media
  ADD CONSTRAINT FK_80D4C5414E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id);
ALTER TABLE media__gallery_media
  ADD CONSTRAINT FK_80D4C541EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id);

ALTER TABLE fournisseur
  ADD logo_id INT DEFAULT NULL;
ALTER TABLE fournisseur
  ADD CONSTRAINT FK_369ECA32F98F144A FOREIGN KEY (logo_id) REFERENCES media__media (id);
CREATE UNIQUE INDEX UNIQ_369ECA32F98F144A
  ON fournisseur (logo_id);

