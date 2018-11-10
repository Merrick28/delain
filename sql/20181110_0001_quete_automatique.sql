
INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TUER #RACE', 'Tuer - Tuer des Monstres d''une certaine race.', 'Dans cette étape on demande au joueur des monstres de certaines races.',
           '[1:delai|1%1],[2:race|0%0],[3:valeur|1%1]',
           'Délai alloué pour cette étape.' ||
           '|C''est la liste des races de monstre à tuer.' ||
           '|C''est le nombre total de monstre à tuer, si ce nombre est supérieur au nombre de race alors au moins un représentant de chaque race devra être tué.',
           'Les [2] ont infestés les environs, tuez au moins [3] représentants de cette/ces races.');

