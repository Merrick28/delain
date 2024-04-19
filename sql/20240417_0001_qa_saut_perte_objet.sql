
INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #DETRUIRE #OBJET',
            'Objets - Aller à l''ETAPE sur condition perte d''objet',
            'Cette étape sert faire un saut d''étape conditionné par la perte d''objet du meneur, l''etape est toujours traitée immédiatement, il n''est pas necessaire de mettre du texte d''étape. ATTENTION: si le joueur possède les objets demandés ils seront immédiatement détruits.',
            '[1:valeur|1%1],[2:objet_generique|0%0],[3:etape|0%0],[4:etape|1%1]',
            'C''est le nombre d''objet maximum qui sera detruit de l''inventaire du meneur en détruisant si possible un objet de chaque type|' ||
            'Ce sont les génériques qui servent de modèle aux objets demandés.|' ||
            'La quête se poursuivra à l''étape déterminé par le nombre d''objet perdu, Si le nombre d''objet réelement perdu est supérieur au nombre d''étape la quete se poursuivra à la dernière étape de cette liste.|' ||
            'Si le perso ne possède aucun des objets demandés (aucun objet détruits), la quête continuera à cette étape.',
            '');
