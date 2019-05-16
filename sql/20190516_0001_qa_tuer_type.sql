INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TUER #TYPE', 'Tuer - Tuer des Monstres d''un certain type.', 'Dans cette étape on demande au joueur de tuer des monstres de certains types.',
           '[1:delai|1%1],[2:type_monstre_generique|0%0],[3:valeur|1%1]',
           'Délai alloué pour cette étape.' ||
           '|C''est la liste des types de monstre à tuer.' ||
           '|C''est le nombre total de monstre à tuer, si ce nombre est supérieur au nombre de type alors au moins un représentant de chaque type devra être tué.',
           'Les [2] ont infestés les environs, tuez au moins [3] représentants de ce/ces types.');
