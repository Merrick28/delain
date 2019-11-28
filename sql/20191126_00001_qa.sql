
INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #INTERACTION', 'Quête - Aller à l''ETAPE sur condition (interaction)', 'Cette étape sert faire un saut d''étape conditionné par la saisie d''un texte par le joueur.',
           '[1:position|1%0],[2:valeur|1%1],[3:etape|1%1],[4:choix_etape|1%0],[5:texte|0%0]',
           'C''est la où le joueur doit-être pour interagir.' ||
           '|C''est le nombre de tentative maximum laissé au joueur (mettre 0 pour illimité)' ||
           '|La quête continuera à cette étape si le nombre de tentative est atteint.'
           '|C''est une liste de mot qui s''ils sont donnés par le joueurs vont lui permettre d''aller vers l''etape correspondante. Pour chaque ligne, plusieurs mot peuvent être fournis séparés par le caractère &verbar;.' ||
           '|Serie de textes qui seront fournis au joueur, 1 par 1 dans l''ordre après chaque tentative infructueuse, s''il y a plus de tentative que de texte, le dernier texte sera redonné.',
            NULL);



INSERT INTO quetes.aquete_type_carac(aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type) VALUES
  (26, 'Nombre de lock', 'VARIABLE');
UPDATE quetes.aquete_type_carac SET aqtypecarac_nom='Nombre de Bzf'  WHERE aqtypecarac_cod=8 ;
UPDATE quetes.aquete_type_carac SET aqtypecarac_nom='Nombre de PA'  WHERE aqtypecarac_cod=9 ;

