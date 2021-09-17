 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #MECA',
            'Quête - Aller à l''ETAPE sur condition (état de mécanisme)',
            'Cette étape sert faire un saut d''étape conditionné par l''état de mécanisme.',
            '[1:meca_etat|0%0],[2:valeur|1%1],[3:etape|1%1],[4:etape|1%1]',
            'Liste des mécanismes et leur état attendu.|' ||
            'Le nombre minimum de mécanisme qui doivent être dans le bon état (laisser vide ou 0 pour tous).|' ||
            'La quête continuera à cette étape si le nombre de mécanisme dans le bon état n''est pas atteint.|' ||
            'La quête continuera à cette étape si le nombre de mécanisme dans le bon état est atteint.' ,
            '');
