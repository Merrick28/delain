 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #CODE',
            'Quête - Aller à l''ETAPE sur condition (saisie d''un code)',
            'Cette étape sert faire un saut d''étape conditionné par saisie d''un code valide par le joueur.',
            '[1:selecteur|1%1|{0~Numérique},{1~Alphabétique},{2~Alphanumérique}],[2:texte|1%1],[3:valeur|1%1],[4:etape|1%1],[5:etape|1%1]',
            'Type du Cryptex (code numérique, alphabétique, ou les deux).|' ||
            'C''est le code à trouver.|' ||
            'Eventuellement le nombre de PA a dépenser pour essayer un code.|' ||
            'La quête continuera à cette étape si le joueur refuse de perdre les PA ou ne done pas le bon code.|' ||
            'La quête continuera à cette étape si le joueur possède les PA necessaires et accepte de les perdre, et donne le bon code.' ,
            'Pour continuer, essayez de trouver le bon code !');
