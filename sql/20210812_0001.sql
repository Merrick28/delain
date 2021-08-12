 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#QUETE #DESACTIVATION',
            'Quête - Système - Désactiver une interaction/quête',
            'Cette étape sera automatiquement validée, elle sert à désactiver cette interaction/quête elle même ou une autre interaction/quête.',
            '[1:quete|1%0]',
            'La quête à désactiver (laisser vide pour désactiver cette quête elle même).' ,
            '');

 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#QUETE #ACTIVATION',
            'Quête - Système - Activer une interaction/quête',
            'Cette étape sera automatiquement validée, elle sert à activer une autre quête.',
            '[1:quete|1%0]',
            'La quête à activer.' ,
            '');
