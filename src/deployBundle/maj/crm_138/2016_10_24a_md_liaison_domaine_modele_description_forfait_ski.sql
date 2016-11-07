ALTER TABLE domaine ADD modele_description_forfait_ski_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE domaine ADD CONSTRAINT FK_78AF0ACC8B0F813C FOREIGN KEY (modele_description_forfait_ski_id) REFERENCES modele_description_forfait_ski (id);
CREATE UNIQUE INDEX UNIQ_78AF0ACC8B0F813C ON domaine (modele_description_forfait_ski_id);