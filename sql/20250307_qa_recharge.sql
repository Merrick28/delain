
INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#RECHARGER #OBJET',
            'Objets - Recharger un objet',
            'Cette étape sert à recharger un objet du joueur moyennant Bzf.',
            '[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1],[6:valeur|1%1]',
            'Délai alloué pour cette étape.|' ||
                                    'Le joueur doit se trouver sur la case de ce PNJ pour faire la recharge.|' ||
                                    'Liste des types d''objet rechargeable (0 pour tous les types)|' ||
                                    'Liste des generiques d''objet rechargeable (0 pour tous génériques)|' ||
                                    'Tarif des réparations en Bzf (pour chaque unité de recharge)|' ||
                                    'Nombre maximum de recharge (vide ou 0 pour sans limite)',
            'Choisisez un objet, [2] vous le rechargera pour le prix indiqué');
