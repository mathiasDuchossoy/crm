ALTER TABLE prestation_annexe DROP FOREIGN KEY FK_81CE635F7925BE19;
ALTER TABLE prestation_annexe DROP FOREIGN KEY FK_81CE635FF6BD1646;
DROP INDEX IDX_81CE635F7925BE19 ON prestation_annexe;
DROP INDEX IDX_81CE635FF6BD1646 ON prestation_annexe;
ALTER TABLE prestation_annexe DROP prestation_annexe_unifie_id, DROP site_id, DROP actif, DROP type;

DROP TABLE `prestation_annexe_unifie`;