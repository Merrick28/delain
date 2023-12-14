INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#NETTOYAGE #ZONE',
            'Quête - Nettoyer une zone ou un étage',
            'Cette étape sert faire un nettoyage des objets/monstres/passages autour d''une zone donnée ',
            '[1:position|0%0],[2:valeur|1%1],[3:selecteur|1%1|{0~Non},{1~Oui}],[4:type_objet|0%0],[5:selecteur|1%1|{0~Non},{1~Oui}],[6:selecteur|1%1|{0~Non},{1~Oui}]',
            'Position ciblé par le nettoyage.|' ||
            'Distance de nettoyage (0 pour nettoyer tout l''étage).|' ||
            'Nettoyer l''or?|' ||
            'Type d''objet à nettoyer?|' ||
            'Nettoyer les passages?|' ||
            'Nettoyer les monstres?',
            '');
