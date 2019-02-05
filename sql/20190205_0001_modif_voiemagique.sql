ALTER TABLE perso_rituel_caracs ADD COLUMN prcarac_type_rituel integer DEFAULT 1;
UPDATE perso_rituel_caracs set prcarac_type_rituel=1 where prcarac_type_rituel is null;
ALTER TABLE perso_rituel_caracs ALTER COLUMN prcarac_type_rituel SET NOT NULL;
COMMENT ON COLUMN perso_rituel_caracs.prcarac_type_rituel IS 'Type de rituel: 1=modifs caracs, 2=modifs voie magique';