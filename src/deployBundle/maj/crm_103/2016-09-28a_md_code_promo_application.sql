CREATE TABLE code_promo_application (id INT AUTO_INCREMENT NOT NULL, code_promo_id INT UNSIGNED DEFAULT NULL, application SMALLINT NOT NULL, INDEX IDX_1351494F294102D4 (code_promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE code_promo_application ADD CONSTRAINT FK_1351494F294102D4 FOREIGN KEY (code_promo_id) REFERENCES code_promo (id);
