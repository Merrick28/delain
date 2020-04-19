INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #ALEATOIRE',
            'Quête - Aller à l''ETAPE sur condition (aléatoire)',
            'Cette étape sert faire un saut d''étape aléatoire, il n''est pas necessaire de mettre du texte d''étape. Un nombre aléatoire enter 0 et 100 va déterminer le saut d''étape',
            '[1:valeur|0%0],[2:etape|0%0]',
            'C''est une liste de valeur de 1 à 100 qui va conditionner le saut d''étape. Chaque nombre saisi représente le % de chance d''aller à l''étape correspondante. ' ||
                'La somme des nombres ne doit pas dépasser 100%. Si la somme est inférieure à 100 et qu''aucune étape étape ne correspont au tirage aléatoire, ' ||
                'la quête se pousuivra vers vers l''étape suivante. En cas d''erreur la quête se poursuivra à l''étape suivante.|' ||
            'C''est la liste des étapes associées à chaque valeur du paramètre précédent, il doit y avoir autant d''étape que de valeur.',
            '');

