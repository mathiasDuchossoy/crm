ALTER TABLE interlocuteur_user
  CHANGE username username VARCHAR(180) NOT NULL,
  CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL,
  CHANGE email email VARCHAR(180) NOT NULL,
  CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL;
ALTER TABLE utilisateur_user
  CHANGE username username VARCHAR(180) NOT NULL,
  CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL,
  CHANGE email email VARCHAR(180) NOT NULL,
  CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL;
ALTER TABLE client_user
  CHANGE username username VARCHAR(180) NOT NULL,
  CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL,
  CHANGE email email VARCHAR(180) NOT NULL,
  CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL;
