

UPDATE quetes.aquete_etape_modele SET
    aqetapmodel_parametres ='[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1],[6:valeur|1%1]',
    aqetapmodel_param_desc = 'Délai alloué pour cette étape.|' ||
            'Le joueur doit se trouver sur la case de ce PNJ pour faire la réparation.|' ||
            'Liste des types d''objet réparable (0 pour tous les types)|' ||
            'Liste des generiques d''objet réparable (0 pour tous génériques)|' ||
            'Tarif des réparations en Bzf (pour chaque 1% de casse)|' ||
            'Nombre maximum d''objet à réparer (vide ou 0 pour sans limite)'
    WHERE aqetapmodel_tag = '#REPARER #OBJET' ;
