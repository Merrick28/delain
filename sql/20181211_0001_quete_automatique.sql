
INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #ETAPE', 'Quête - Aller à l''ETAPE sur condition (étape)', 'Cette étape sert faire un saut d''étape conditionné par la réalisation d''autres étapes, il n''est pas necessaire de mettre du texte d''étape',
           '[1:quete_etape|0%0],[2:etape|1%1]',
           'C''est une liste d''étape de cette quête ou d''autres quêtes qui conditionne le saut d''etape.' ||
           '|C''est l''étape de destination souhaitée si les étapes listées ont bien toutes été réalisées.',
            NULL);