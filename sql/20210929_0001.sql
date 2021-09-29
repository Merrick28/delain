 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #CARAC',
            'Quête - Aller à l''ETAPE sur condition (jet de carac)',
            'Cette étape sert faire un saut d''étape conditionné par un jet de dé sous une caractéristique du meneur, il n''est pas necessaire de mettre du texte d''étape.',
            '[1:selecteur|1%1|{1~Force},{2~Dextérité},{3~Intelligence},{4~Constitution},{5~Vue}],[2:valeur|1%1],[3:valeur|1%1],[4:etape|1%1],[5:etape|1%1],[6:etape|1%1],[7:etape|1%1]',
            'La caractéristique  qui sera évaluée.|' ||
            'C''est le niveau de carac minimum requis. Si le perso ne possède pas la compétence ou n''a pas le niveau requis la quête se poursuit à l''étape suivante.|' ||
            'C''est la valeur d''opposition, après le jet du joueur qui dertermine une réussite critique ou un echec critique, le système réalise un jet d''opposition sous cette caractéristique. Il n''y a pas de réussite special.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite critique du jet de carac.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite sur le jet de carac.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas d''échec.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas d''échec critique.',
            '');

