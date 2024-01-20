


INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#REPARER #OBJET',
            'Objets - Réparer un objet',
            'Cette étape sert à réparer un objet du joueur moyennant Bzf.',
            '[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1]',
            'Délai alloué pour cette étape.|' ||
            'Le joueur doit se trouver sur la case de ce PNJ pour faire la réparation.|' ||
            'Liste des types d''objet réparable (0 pour tous les types)|' ||
            'Liste des generiques d''objet réparable (0 pour tous génériques)|' ||
            'Tarif des réparations en Bzf (pour chaque 1% de casse)',
            'Choisisez un objet, [2] vous le réparera pour le prix indiqué');
