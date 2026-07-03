


INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
VALUES ('#SAUT #CONDITION #TRANSACTION',
        'Quête - Aller à l''ETAPE sur condition (objet en transaction)',
        'Dans cette étape on recherche si le perso a mis un objet en transaction (au prix de 0 bzf), le saut d''étape dependra du générique d''objet echangé.',
        '[1:perso|1%0],[2:objet_generique|0%0],[3:etape|0%0],[4:etape|1%1],[5:etape|1%1],[6:selecteur|1%1|{0~Recupérer},{1~Supprimer},{2~Annuler}],[7:selecteur|1%1|{0~Recupérer},{1~Supprimer},{2~Annuler}]',
        'C''est le perso qui attente la transaction.|' ||
            'C''est la liste des types des objets générique attendus.|' ||
            'C''est la liste des étapes de saut en fonction de la liste des objets génériques attendue (il doit y avoir 1 etape par objet).|' ||
            'C''est l''etape de saut si un objet est trouvé en transaction mais que son générique n''est pas dans la liste des objets attendus.|' ||
            'C''est l''étape de saut si aucun objet n''est trouvé en transaction.|' ||
            'Indiquer ce que l''on fait de l''objet, s''il a été trouvé <b>dans la liste</b>: on le recupère, on le supprime, on annule la transaction.|'||
            'Indiquer ce que l''on fait de l''objet, s''il <b>n''est PAS dans la liste</b> des génériques attendus: on le recupère, on le supprime, on annule la transaction.',
            '');
