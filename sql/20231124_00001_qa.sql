


INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #EQUIPE',
            'Quête - Aller à l''ETAPE sur condition (faire des équipes)',
            'Cette étape sert faire un saut d''étape conditionné par un l''inscription du meneur dans une équipe constituer d''autres meneurs de cette même quete.',
            '[1:delai|1%1],[2:valeur|1%1],[3:valeur|1%1],[4:selecteur|1%1|{0~Triplette autorisée},{1~Limite à 1 membre par triplette}],[5:etape|1%0],[6:etape|1%1]',
            'Délai alloué pour cette étape.|' ||
            'Nombre de joueur mini par équipe|' ||
            'Nombre de joueur maxi par équipe (0 = sans limite)|' ||
            'Utilisateur des persos d''un même joueur dans les équipes|' ||
            'Liste des étapes (il y aura autant d''équipe que d''étape de saut)|' ||
            'Etape de rejet (en cas d''erreur triplette ou si le joueur quitte en cours de préparation)',
            '');
