ALTER TABLE quetes.aquete ADD COLUMN aquete_nom_alias text;
ALTER TABLE quetes.aquete ALTER COLUMN aquete_nom_alias SET DEFAULT ''::text;
UPDATE quetes.aquete set aquete_nom_alias = aquete_nom ;
ALTER TABLE quetes.aquete ALTER COLUMN aquete_nom_alias SET NOT NULL;
COMMENT ON COLUMN quetes.aquete.aquete_nom_alias IS 'Le nom de la quete (pour les admins)';

ALTER TABLE quetes.aquete ADD COLUMN aquete_journal_archive character varying;
ALTER TABLE quetes.aquete ALTER COLUMN aquete_journal_archive SET DEFAULT 'O'::character varying;
UPDATE quetes.aquete set aquete_journal_archive = 'O' ;
COMMENT ON COLUMN quetes.aquete.aquete_journal_archive IS 'Si à N, la quête ne sera archivée dans le journal des quêtes terminées.';

UPDATE quetes.aquete_etape_modele
SET aqetapmodel_parametres = '[1:quete_etape|0%0],[2:etape|1%1],[3:etape|1%1]',
    aqetapmodel_param_desc = 'C''est une liste d''étapes de cette quête ou d''autres quêtes qui conditionne le saut d''etape<br><b><u>Attention</u></b>: la vérification est faite avec le journal des quêtes terminées, et certaines quêtes n''archivent pas leurs réalisations.' ||
                             '|C''est l''étape de destination souhaitée si les étapes listées ont bien toutes été réalisées.' ||
                             '|La quête continuera à cette étape si au moins une des étapes n''a pas été réalisées.'
WHERE aqetapmodel_tag='#SAUT #CONDITION #ETAPE';
