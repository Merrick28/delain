
INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #MULTIPLE #CONDITION #PERSO',
            'Quête - Aller à l''ETAPE sur répartition de perso',
            'Cette étape sert faire un saut d''étape conditionné par les carastéristiques du meneur, il n''est pas necessaire de mettre du texte d''étape.',
            '[1:perso_condition_liste|0%0],[2:etape|0%0],[3:etape|1%1]',
            'Liste de groupe conditions que le perso doit vérifier. La première condition doit être du type Nouveau Groupe.|' ||
            'Il doit y avoir autant d''étape que de groupe de contition. La quête se poursuit sur l''une des étapes de la liste en fonction du groupe conditionnel vérifié par le perso.|' ||
            'Si le perso ne vérifie aucun des groupes de condition, la quête continuera à cette étape.',
            '');

DROP FUNCTION quetes.aq_verif_perso_condition_etape(integer,integer,integer,integer);
