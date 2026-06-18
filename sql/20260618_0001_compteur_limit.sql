ALTER TABLE IF EXISTS compteur
    ADD COLUMN compteur_min numeric DEFAULT NULL;

ALTER TABLE IF EXISTS compteur
    ADD COLUMN compteur_max numeric DEFAULT NULL;

ALTER TABLE compteur
    ALTER COLUMN compteur_init TYPE numeric;