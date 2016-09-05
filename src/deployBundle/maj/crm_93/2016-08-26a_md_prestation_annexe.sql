DROP TABLE `prestation_annexe_sous_famille_prestation_annexe`;

ALTER TABLE prestation_annexe ADD sous_famille_prestation_annexe_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe ADD CONSTRAINT FK_81CE635F3AB7F643 FOREIGN KEY (sous_famille_prestation_annexe_id) REFERENCES sous_famille_prestation_annexe (id);
CREATE INDEX IDX_81CE635F3AB7F643 ON prestation_annexe (sous_famille_prestation_annexe_id);
ALTER TABLE sous_famille_prestation_annexe ADD CONSTRAINT FK_F81CF9D05D1D40E4 FOREIGN KEY (famille_prestation_annexe_id) REFERENCES famille_prestation_annexe (id);
ALTER TABLE sous_famille_prestation_annexe_traduction ADD CONSTRAINT FK_29BB02A23AB7F643 FOREIGN KEY (sous_famille_prestation_annexe_id) REFERENCES sous_famille_prestation_annexe (id);
ALTER TABLE sous_famille_prestation_annexe_traduction ADD CONSTRAINT FK_29BB02A22AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);