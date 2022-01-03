 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#CHANGE #IMPALPABILITE',
            'Gain/Perte - Mettre le perso et/ou son familier tangible/intangible.',
            'Cette étape sert à mettre le perso et/ou son familier intangible/tangible, elle est automatiquement réussie.',
            '[1:selecteur|1%1|{1~Le meneur},{2~Son familier},{3~Le meneur et son familer}],[2:valeur|1%1]',
            'La cible du changement d''impalpabilité.|' ||
            'Le nombre de tour d''impalpabilité (mettre 0 pour rendre le perso tangible)',
            '');
