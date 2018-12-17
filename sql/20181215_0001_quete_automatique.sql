
INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TELEPORTATION #PORTAIL #PERSO', 'Téléportation - Créer un portail sur un Perso', 'Cette étape sert à créer un portail de téléportation au départ d''un perso, il n''est pas necessaire de mettre du texte d''étape',
           '[1:perso|1%1],[2:position|1%1],[3:valeur|1%1],[4:valeur|1%1]',
           'C''est le perso sur lequel le portail sera ouvert. Laisser à 0 pour un départ sur la position de l''aventurier qui fait la quête.' ||
           '|C''est la position centrale où débouchera le portail de sortie (attention les entrées/sortie des arène de combat ne peuvent se faire que vers/depuis d''autres arènes de combats).' ||
           '|C''est la dispersion, le portail sera positionnée autour du point ciblé, dans un rayon inférieur à cette valeur.' ||
           '|C''est le délai (en heure) pendant lequel le portail restera ouvert.',
            NULL);

INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TELEPORTATION #PORTAIL #POSITION', 'Téléportation - Créer un portail depuis une position', 'Cette étape sert à créer un portail de téléportation au départ d''une position définie, il n''est pas necessaire de mettre du texte d''étape',
           '[1:position|1%1],[2:position|1%1],[3:valeur|1%1],[4:valeur|1%1]',
           'C''est la position de départ du portail.' ||
           '|C''est la position centrale où débouchera le portail de sortie (attention les entrées/sortie des arène de combat ne peuvent se faire que vers/depuis d''autres arènes de combats).' ||
           '|C''est la dispersion, le portail sera positionnée autour du point ciblé, dans un rayon inférieur à cette valeur.' ||
           '|C''est le délai (en heure) pendant lequel le portail restera ouvert.',
            NULL);

INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TELEPORTATION #PERSO', 'Téléportation - Téléportation immédiate de persos', 'Cette étape sert à téléporter immédiatement des persos vers une poistion donnée, il n''est pas necessaire de mettre du texte d''étape',
           '[1:position|1%1],[2:valeur|1%1],[3:selecteur|1%1|{1~le meneur de quête seul},{2~le meneur et sa triplette à proximité},{3~le meneur et sa coterie à proximité},{4~tous les aventuriers à proximité}],[4:valeur|1%1]',
           'C''est la position centrale où seront téléportés le ou les perso.' ||
           '|C''est la dispersion qui sera appliquée à chaque perso téléporté, il le sera autour du point central dans un rayon inférieur à cette valeur.' ||
           '|Ce sont les persos situés à proximité qui doivent être téléportés.' ||
           '|Distance de proximité, au dela de cette distance les persos ne seront pas emmenés avec le meneur.',
            NULL);