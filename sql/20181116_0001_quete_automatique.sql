update quetes.aquete_etape_modele 
set aqetapmodel_param_desc = 'C''est le nombre de monstre à invoquer.' ||
           '|C''est le perso autour duquel les monstres seront invoqués, laisser à zéro pour faire l''invocation autour du perso qui fait la quête.' ||
           '|C''est la dispersion. Les monstres seront invoqués autour du point ciblé, dans un rayon inférieur à cette valeur.' ||
           '|Ce paramètre n''est pas éditable mais il peut être utilisé dans le reste de la quête par référence, il contiendra la liste des monstres qui auront été invoqués.'
           where aqetapmodel_tag= '#MONSTRE #ARMEE'