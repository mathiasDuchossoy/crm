ALTER TABLE interlocuteur_user CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL;
CREATE UNIQUE INDEX UNIQ_F74ED340C05FB297 ON interlocuteur_user (confirmation_token);
ALTER TABLE utilisateur_user CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL;
CREATE UNIQUE INDEX UNIQ_4412BA73C05FB297 ON utilisateur_user (confirmation_token);
ALTER TABLE client_user CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL;
CREATE UNIQUE INDEX UNIQ_5C0F152BC05FB297 ON client_user (confirmation_token);