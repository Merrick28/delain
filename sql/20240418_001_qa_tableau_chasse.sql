
INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TUER #TABLEAU #CHASSE',
            'Tuer - Type de monstre au tableau de chasse.',
            'Dans cette étape on demande au joueur d''avoir un certain nombre de monstre d''un certain type à son tableau de chasse.',
            '[1:delai|1%1],[2:selecteur|1%1|{0~Tableau de chasse},{1~Achevé en solo}],[3:type_monstre_generique|0%0],[4:valeur|1%1]',
            'Délai alloué pour cette étape.|' ||
            'Indiquer si les monstres doivent figurer au tableau de chasse ou au tableau solo.|'||
            'C''est la liste des types de monstre générique à tuer.|' ||
            'C''est le nombre total que le joueur doit avoir à son tableau de chasse en faisant la somme de chaque type demandé' ,
            '');
