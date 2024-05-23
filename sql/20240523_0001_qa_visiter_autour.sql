INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#MOVE #VISITER #ZONE',
            'Déplacement - Visiter un étage ou une zone.',
            'Dans cette étape on demande au joueur d''explorer une ou plusieurs zones spécifiques ou de visiter un certain % d''étages.',
            '[1:delai|1%1],[2:position|0%0],[3:valeur|0%0],[4:valeur|0%0]',
            'Délai alloué pour cette étape.|' ||
            'Les positions qui doivent être visitées (pour l''étage entier, mettre une case de celui-ci et 0 dans la taille de zone)|'||
            'La taille (en nombre de case) de chaques zones à visiter (si c''est la même taille pour toutes les zones il est possible de ne mettre qu''une seule valeur, sinon mettre une taille par position).|' ||
            'Pourcentage de visite requis pour chaques zones (si c''est le même pourcentage pour toutes les zones il est possible de ne mettre qu''une seule valeur, sinon mettre un pourcentage par position).|' ,
            '');
