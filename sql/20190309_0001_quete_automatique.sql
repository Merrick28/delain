
INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #DIALOGUE', 'Quête - Aller à l''ETAPE sur condition (dialogue)', 'Cette étape sert faire un saut d''étape conditionné par la saisie d''un texte par le joueur.',
           '[1:valeur|1%1],[2:etape|1%1],[3:choix_etape|1%0],[4:texte|0%0]',
           'C''est le nombre de tentative maximum laissé au joueur (mettre 0 pour illimité)' ||
           '|La quête continuera à cette étape si le nombre de tentative est atteint.'
           '|C''est une liste de mot qui s''ils sont donnés par le joueurs vont lui permettre d''aller vers l''etape correspondante. Pour chaque ligne, plusieurs mot peuvent être fournis séparés par le caractère &verbar;.' ||
           '|Serie de textes qui seront fournis au joueur, 1 par 1 dans l''ordre après chaque tentative infructueuse, s''il y a plus de tentative que de texte, le dernier texte sera redonné.',
            NULL);

