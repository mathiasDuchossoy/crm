CREATE TABLE periode (
  id      INT AUTO_INCREMENT NOT NULL,
  type_id INT DEFAULT NULL,
  debut   DATE               NOT NULL,
  fin     DATE               NOT NULL,
  nb_jour SMALLINT           NOT NULL,
  INDEX IDX_93C32DF3C54C8C93 (type_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE jms_cron_jobs (
  id        INT UNSIGNED AUTO_INCREMENT NOT NULL,
  command   VARCHAR(200)                NOT NULL,
  lastRunAt DATETIME                    NOT NULL,
  UNIQUE INDEX UNIQ_55F5ED428ECAEAD4 (command),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE jms_jobs (
  id              BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  state           VARCHAR(15)                    NOT NULL,
  queue           VARCHAR(50)                    NOT NULL,
  priority        SMALLINT                       NOT NULL,
  createdAt       DATETIME                       NOT NULL,
  startedAt       DATETIME          DEFAULT NULL,
  checkedAt       DATETIME          DEFAULT NULL,
  workerName      VARCHAR(50)       DEFAULT NULL,
  executeAfter    DATETIME          DEFAULT NULL,
  closedAt        DATETIME          DEFAULT NULL,
  command         VARCHAR(255)                   NOT NULL,
  args            LONGTEXT                       NOT NULL
  COMMENT '(DC2Type:json_array)',
  output          LONGTEXT          DEFAULT NULL,
  errorOutput     LONGTEXT          DEFAULT NULL,
  exitCode        SMALLINT UNSIGNED DEFAULT NULL,
  maxRuntime      SMALLINT UNSIGNED              NOT NULL,
  maxRetries      SMALLINT UNSIGNED              NOT NULL,
  stackTrace      LONGBLOB          DEFAULT NULL
  COMMENT '(DC2Type:jms_job_safe_object)',
  runtime         SMALLINT UNSIGNED DEFAULT NULL,
  memoryUsage     INT UNSIGNED      DEFAULT NULL,
  memoryUsageReal INT UNSIGNED      DEFAULT NULL,
  originalJob_id  BIGINT UNSIGNED   DEFAULT NULL,
  INDEX IDX_704ADB9349C447F1 (originalJob_id),
  INDEX cmd_search_index (command),
  INDEX sorting_index (state, priority, id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE jms_job_dependencies (
  source_job_id BIGINT UNSIGNED NOT NULL,
  dest_job_id   BIGINT UNSIGNED NOT NULL,
  INDEX IDX_8DCFE92CBD1F6B4F (source_job_id),
  INDEX IDX_8DCFE92C32CF8D4C (dest_job_id),
  PRIMARY KEY (source_job_id, dest_job_id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE jms_job_related_entities (
  job_id        BIGINT UNSIGNED NOT NULL,
  related_class VARCHAR(150)    NOT NULL,
  related_id    VARCHAR(100)    NOT NULL,
  INDEX IDX_E956F4E2BE04EA9 (job_id),
  PRIMARY KEY (job_id, related_class, related_id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE jms_job_statistics (
  job_id         BIGINT UNSIGNED  NOT NULL,
  characteristic VARCHAR(30)      NOT NULL,
  createdAt      DATETIME         NOT NULL,
  charValue      DOUBLE PRECISION NOT NULL,
  PRIMARY KEY (job_id, characteristic, createdAt)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE periode
  ADD CONSTRAINT FK_93C32DF3C54C8C93 FOREIGN KEY (type_id) REFERENCES type_periode (id);
ALTER TABLE jms_jobs
  ADD CONSTRAINT FK_704ADB9349C447F1 FOREIGN KEY (originalJob_id) REFERENCES jms_jobs (id);
ALTER TABLE jms_job_dependencies
  ADD CONSTRAINT FK_8DCFE92CBD1F6B4F FOREIGN KEY (source_job_id) REFERENCES jms_jobs (id);
ALTER TABLE jms_job_dependencies
  ADD CONSTRAINT FK_8DCFE92C32CF8D4C FOREIGN KEY (dest_job_id) REFERENCES jms_jobs (id);
ALTER TABLE jms_job_related_entities
  ADD CONSTRAINT FK_E956F4E2BE04EA9 FOREIGN KEY (job_id) REFERENCES jms_jobs (id);