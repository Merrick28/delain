INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#TUER #ZONE',
            'Tuer - Eliminer les monstres autour d''une position.',
            'Dans cette étape on demande au joueur d''éliminer tous les monstres autour d''une certaine position.',
            '[1:delai|1%1],[2:position|1%1],[3:valeur|1%1],[4:type_monstre_generique|0%0],[5:type_monstre_generique|0%0]',
            'Délai alloué pour cette étape.|' ||
            'La position qui doit être nettoyée de ses monstres|'||
            'La taille (en nombre de case) de la zone à nettoyer.|' ||
            'Liste des monstres génériques à nettoyer dans la zone (laisser vide pour tous les monstres).|' ||
            'Liste des monstres génériques à exclure du nettoyage (laisser vide pour ne pas faire d''exclusion)' ,
            'Eliminez tous les monstres autour de [2] dans un rayon de [3]!');
