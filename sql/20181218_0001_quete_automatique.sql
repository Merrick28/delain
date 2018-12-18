UPDATE quetes.aquete_etape_modele
SET aqetapmodel_parametres = '[1:quete_etape|0%0],[2:etape|1%1],[3:etape|1%1]',
    aqetapmodel_param_desc = 'C''est une liste d''étape de cette quête ou d''autres quêtes qui conditionne le saut d''etape.' ||
                             '|C''est l''étape de destination souhaitée si les étapes listées ont bien toutes été réalisées.' ||
                             '|C''est l''étape si au moins une des étape n''a pas été réalisées.'
WHERE aqetapmodel_tag='#SAUT #CONDITION #ETAPE';
