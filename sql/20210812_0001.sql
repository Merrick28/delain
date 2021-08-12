 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#QUETE #DESACTIVATION',
            'Quête - Système - Désactiver une quête',
            'Cette étape sera automatiquement validée, elle sert à désactiver cette quête elle même ou une autre quête.',
            '[1:quete|1%0]',
            'La quête à désactiver (laisser vide pour désactiver cette quête elle même).' ,
            '');

 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#QUETE #ACTIVATION',
            'Quête - Système - Activer une quête',
            'Cette étape sera automatiquement validée, elle sert à activer une autre quête.',
            '[1:quete|1%0]',
            'La quête à activer.' ,
            '');

 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #PA',
            'Quête - Aller à l''ETAPE sur condition (consommation de PA)',
            'Cette étape sert faire un saut d''étape conditionné par la validation du joueur a une consommation de PA. Si validé, les Points d''Actions du joueur sont consommmés. Une confirmation sera demandé au joueur par « OUI » ou « NON ».',
            '[1:valeur|1%1],[2:etape|1%1],[3:etape|1%1]',
            'C''est le nombre de PA nécéssaire qui seront consommés.|' ||
            'La quête continuera à cette étape si le joueur refuse de perdre les PA.|' ||
            'La quête continuera à cette étape si le joueur possède les PA necessaires et accepte de les perdre, les PA sont consommés.' ,
            'Pour continuer accepter vous de perdre [1] PA ?');
