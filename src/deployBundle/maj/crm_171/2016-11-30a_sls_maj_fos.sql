ALTER TABLE interlocuteur_user DROP locked, DROP expires_at, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL;
ALTER TABLE utilisateur_user DROP locked, DROP expires_at, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_user DROP locked, DROP expires_at, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL;