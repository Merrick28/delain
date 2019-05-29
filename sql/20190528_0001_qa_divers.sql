INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#RECEVOIR #INSTANT #OBJET', 'Objet - Recevoir immédiatement un objet.', 'Le joueur reçoit immédiatement le ou les objets.',
           '[1:valeur|1%1],[2:objet_generique|0%0]',
           'C''est le nombre d''objet à donner, si ce nombre est inférieur au nombre de générique alors un tirage aléatoire sera effectué, s''il est superieur alors le joueur recevra au moins un exemplaire de chaque puis un complément aléatoire pour atteidre le nombre prévu.' ||
           '|Ce sont les génériques qui serviront de modèle aux objets qui seront donnés.',
           'Vous recevez immédiatement [1] objet(s) parmi: [2].');


INSERT INTO bonus_type( tbonus_libc, tonbus_libelle, tbonus_gentil_positif, tbonus_nettoyable)
    VALUES ( 'JDC', 'Jus de Chronomètre', true, 'O');


INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#RECEVOIR #BONUS', 'Gain - Recevoir un BONUS ou un MALUS.', 'Le joueur reçoit immédiatement le ou les bonus/malus.',
           '[1:valeur|1%1],[2:bonus|0%0]',
           'C''est le nombre de bonus/malus à donner, si ce nombre est inférieur au nombre de bonus/malus total alors un tirage aléatoire sera effectué, s''il est superieur alors le joueur recevra tous les bonus/malus.' ||
           '|Ce sont les bonus/malus qui seront donnés.',
           'Vous recevez immédiatement [1] bonus/malus parmi: [2].');
