
INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TUER #PARTICIPER #MORT',
            'Tuer - Participer à la mort de monstre d''un type ou d''une race spécifique.',
            'Dans cette étape on demande au joueur de participer à la mort de monstre d''un type ou d''une race spécifique.',
            '[1:delai|1%1],[2:valeur|1%1|],[3:race|0%0],[4:type_monstre_generique|0%0]',
            'Délai alloué pour cette étape.|' ||
            'Nombre total de monstre (de race ou de type demandé) pour lequels le meneur devra participer à la mort.|'||
            'C''est la liste des races de monstres à tuer, si cette liste est vide, seule la listes des type de monstre sera pris en compte (au moins 1 des 2 listes doit être non-vide).|' ||
            'C''est la liste des types de monstre à tuer, si cette liste est vide, seule la liste des races de monstre sera prise en compte.' ,
            '');
