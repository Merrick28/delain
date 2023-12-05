update quetes.aquete_etape_modele set
	aqetapmodel_parametres='[1:delai|1%1],[2:craft|1%1],[3:selecteur|1%1|{0~Triplette autorisée},{1~Limite à 1 membre par triplette}],[4:valeur|1%0],[5:etape|1%0],[6:etape|1%1],[7:perso|1%0]',
	aqetapmodel_param_desc= 'Délai alloué pour cette étape.|' ||
            'Num#1 = Nombre d''équipe / Num#2 = Nombre de joueur mini par équipe / Num#3 = Nombre de joueur maxi par équipe (0 = sans limite) / Int#1 = Delai de lancement (en h) / Autres paramètres du craft non-utilisés.|' ||
            'Utilisateur des persos d''un même joueur dans les équipes.|' ||
            'Répartition des sauts d''étape au sein d''une même équipe. Il est possible de d''aiguiller les joueurs d''une même équipe vers des étapes différentes, si vous souhaitez le faire, indiquez successivement le nombre de joueur attendu pour chaque saut, mettre 0 pour le dernier saut.|' ||
            'Liste des étapes, il doit y avoit autant d''étape que le nombre d''équipe x le nombre de saut (4ème paramètre).|' ||
            'Etape de rejet (en cas d''erreur triplette ou si le joueur quitte en cours de préparation).|' ||
            'Liste des persos à laisser vide, la liste sera rempli au fur et à mesure des inscriptions par les joueurs.'
	where aqetapmodel_tag = '#SAUT #CONDITION #EQUIPE' ;
